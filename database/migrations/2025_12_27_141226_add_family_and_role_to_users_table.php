<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // family_id が無ければ追加
            if (!Schema::hasColumn('users', 'family_id')) {
                $table->unsignedBigInteger('family_id')->nullable();
            }

            // role が無ければ追加
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('child');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 必要なら削除処理を書くけど、今回は空でOK
        });
    }
};