// 親ユーザーを作成
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => bcrypt($request->password),
    'role' => 'parent',
]);

// 家族を作成
$family = Family::create([
    'name' => $user->name . ' Family',
    'invite_code' => Str::random(8),
]);

// 親ユーザーを家族に紐づける
$user->family_id = $family->id;
$user->save();

public function family()
{
    return $this->belongsTo(Family::class);
}

public function users()
{
    return $this->hasMany(User::class);
}
