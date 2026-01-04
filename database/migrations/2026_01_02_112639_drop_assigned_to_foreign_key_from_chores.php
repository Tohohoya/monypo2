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
    Schema::table('chores', function (Blueprint $table) {
        // 外部キーを削除
        $table->dropForeign('chores_assigned_to_foreign');
    });
}

public function down()
{
    Schema::table('chores', function (Blueprint $table) {
        $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
    });
}
};
