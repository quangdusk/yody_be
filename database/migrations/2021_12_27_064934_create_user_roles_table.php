<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userId')->nullable()->comment('Mã khách hàng');
            $table->string('roleId')->nullable()->comment("Mã chức danh");
            $table->string('createdBy')->nullable()->comment("Người tạo");
            $table->string('isDeleted')->nullable()->comment("Đã xóa");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}
