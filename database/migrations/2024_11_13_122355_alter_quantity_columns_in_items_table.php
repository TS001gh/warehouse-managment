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
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger('current_quantity')->change();
            $table->unsignedInteger('min_quantity')->change();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('current_quantity')->change();
            $table->integer('min_quantity')->change();
        });
    }
};
