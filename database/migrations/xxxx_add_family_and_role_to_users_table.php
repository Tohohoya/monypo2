<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 家族ID（families テーブルへの外部キー）
            $table->unsignedBigInteger('family_id')->nullable()->after('id');

            // 親 or 子供
            $table->string('role', 20)->default('child')->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['family_id', 'role']);
        });
    }
};
