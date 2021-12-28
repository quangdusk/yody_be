<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->comment("Tên menu");
            $table->string('path')->nullable()->comment("link menu");
            $table->string('icon')->nullable()->comment("Icon menu");
            $table->tinyInteger('active')->nullable()->comment("Trạng thái menu");
            $table->integer('parentId')->nullable()->comment("Id menu cha");
            $table->integer('status')->nullable()->comment("Trạng thái khuyến mãi của menu");
            $table->timestamps();

            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
