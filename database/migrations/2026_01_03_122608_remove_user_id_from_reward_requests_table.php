<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('reward_requests', function (Blueprint $table) {
        // ① 外部キーを削除
        $table->dropForeign(['user_id']);

        // ② カラムを削除
        $table->dropColumn('user_id');
    });
}

public function down()
{
    Schema::table('reward_requests', function (Blueprint $table) {
        // ③ 復元（必要なら）
        $table->unsignedBigInteger('user_id')->nullable();

        // ④ 外部キーを復元
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}
};
