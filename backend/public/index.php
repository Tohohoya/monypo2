<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// データベース接続
$db = new PDO('sqlite:' . __DIR__ . '/../database/app.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$app = AppFactory::create();

// CORS設定
// 注意: 本番環境では '*' ではなく、特定のオリジンを指定してください
// 例: ->withHeader('Access-Control-Allow-Origin', 'https://your-domain.com')
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// ルート定義
$app->post('/api/auth/login', function (Request $request, Response $response) use ($db) {
    $data = json_decode($request->getBody()->getContents(), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $response->getBody()->write(json_encode([
            'success' => true,
            'user' => $user,
            'token' => base64_encode($user['id'] . ':' . time())
        ]));
    } else {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'ログインに失敗しました'
        ]));
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/tasks', function (Request $request, Response $response) use ($db) {
    $stmt = $db->query('SELECT t.*, u.name as created_by_name FROM tasks t 
                        LEFT JOIN users u ON t.created_by = u.id 
                        ORDER BY t.created_at DESC');
    $tasks = $stmt->fetchAll();
    
    $response->getBody()->write(json_encode($tasks));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/tasks', function (Request $request, Response $response) use ($db) {
    $data = json_decode($request->getBody()->getContents(), true);
    
    // Validate input
    if (empty($data['title']) || !isset($data['points']) || !isset($data['created_by'])) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => '必須フィールドが不足しています'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $points = intval($data['points']);
    if ($points <= 0) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'ポイントは正の整数である必要があります'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    // Verify user is an adult
    $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$data['created_by']]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'adult') {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'タスクを作成する権限がありません'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }
    
    $stmt = $db->prepare('INSERT INTO tasks (title, description, points, created_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $data['title'],
        $data['description'] ?? '',
        $points,
        $data['created_by']
    ]);
    
    $response->getBody()->write(json_encode([
        'success' => true,
        'id' => $db->lastInsertId()
    ]));
    
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/tasks/{id}/complete', function (Request $request, Response $response, array $args) use ($db) {
    $data = json_decode($request->getBody()->getContents(), true);
    $taskId = $args['id'];
    $childId = $data['child_id'];
    
    // Validate task exists
    $stmt = $db->prepare('SELECT id FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    if (!$stmt->fetch()) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'タスクが見つかりません'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
    
    // Validate child user exists and is a child
    $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$childId]);
    $user = $stmt->fetch();
    
    if (!$user || $user['role'] !== 'child') {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => '無効な子どもユーザーです'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $stmt = $db->prepare('INSERT INTO task_completions (task_id, child_id, status) VALUES (?, ?, ?)');
    $stmt->execute([$taskId, $childId, 'pending']);
    
    $response->getBody()->write(json_encode([
        'success' => true,
        'id' => $db->lastInsertId()
    ]));
    
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/completions', function (Request $request, Response $response) use ($db) {
    $stmt = $db->query('SELECT tc.*, t.title, t.points, u.name as child_name 
                        FROM task_completions tc
                        LEFT JOIN tasks t ON tc.task_id = t.id
                        LEFT JOIN users u ON tc.child_id = u.id
                        ORDER BY tc.completed_at DESC');
    $completions = $stmt->fetchAll();
    
    $response->getBody()->write(json_encode($completions));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/completions/{id}/approve', function (Request $request, Response $response, array $args) use ($db) {
    $data = json_decode($request->getBody()->getContents(), true);
    $completionId = $args['id'];
    $approvedBy = $data['approved_by'];
    
    $stmt = $db->prepare('UPDATE task_completions SET status = ?, approved_by = ?, approved_at = CURRENT_TIMESTAMP WHERE id = ?');
    $stmt->execute(['approved', $approvedBy, $completionId]);
    
    $response->getBody()->write(json_encode(['success' => true]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/points/{childId}', function (Request $request, Response $response, array $args) use ($db) {
    $childId = $args['childId'];
    
    // 承認されたポイントの合計
    $stmt = $db->prepare('SELECT COALESCE(SUM(t.points), 0) as earned 
                          FROM task_completions tc
                          LEFT JOIN tasks t ON tc.task_id = t.id
                          WHERE tc.child_id = ? AND tc.status = "approved"');
    $stmt->execute([$childId]);
    $earned = $stmt->fetch()['earned'];
    
    // 使用したポイントの合計
    $stmt = $db->prepare('SELECT COALESCE(SUM(points), 0) as used FROM point_usages WHERE child_id = ?');
    $stmt->execute([$childId]);
    $used = $stmt->fetch()['used'];
    
    $response->getBody()->write(json_encode([
        'earned' => $earned,
        'used' => $used,
        'balance' => $earned - $used
    ]));
    
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/points/use', function (Request $request, Response $response) use ($db) {
    $data = json_decode($request->getBody()->getContents(), true);
    
    // Validate input
    if (!isset($data['child_id']) || !isset($data['points']) || empty($data['purpose'])) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => '必須フィールドが不足しています'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $points = intval($data['points']);
    if ($points <= 0) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'ポイントは正の整数である必要があります'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    // Check balance
    $stmt = $db->prepare('SELECT COALESCE(SUM(t.points), 0) as earned 
                          FROM task_completions tc
                          LEFT JOIN tasks t ON tc.task_id = t.id
                          WHERE tc.child_id = ? AND tc.status = "approved"');
    $stmt->execute([$data['child_id']]);
    $earned = $stmt->fetch()['earned'];
    
    $stmt = $db->prepare('SELECT COALESCE(SUM(points), 0) as used FROM point_usages WHERE child_id = ?');
    $stmt->execute([$data['child_id']]);
    $used = $stmt->fetch()['used'];
    
    $balance = $earned - $used;
    
    if ($points > $balance) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'ポイントが不足しています'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $stmt = $db->prepare('INSERT INTO point_usages (child_id, points, purpose) VALUES (?, ?, ?)');
    $stmt->execute([
        $data['child_id'],
        $points,
        $data['purpose']
    ]);
    
    $response->getBody()->write(json_encode([
        'success' => true,
        'id' => $db->lastInsertId()
    ]));
    
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/points/history/{childId}', function (Request $request, Response $response, array $args) use ($db) {
    $childId = $args['childId'];
    
    $stmt = $db->prepare('SELECT * FROM point_usages WHERE child_id = ? ORDER BY used_at DESC');
    $stmt->execute([$childId]);
    $history = $stmt->fetchAll();
    
    $response->getBody()->write(json_encode($history));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
