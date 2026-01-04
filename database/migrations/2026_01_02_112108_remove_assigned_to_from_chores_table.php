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
    //Schema::table('chores', function (Blueprint $table) {
    //    $table->dropColumn('assigned_to');
    //});
}

public function down()
{
    Schema::table('chores', function (Blueprint $table) {
        $table->unsignedBigInteger('assigned_to')->nullable();
    });
}
};
