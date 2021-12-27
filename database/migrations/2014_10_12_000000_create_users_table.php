<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable()->comment("Mã khách hàng");
            $table->string('type')->nullable()->comment("Chức vụ");
            $table->string('name')->comment("Họ và tên");
            $table->string('token')->nullable()->comment("Token");
            $table->string('phone')->comment("Số điện thoại");
            $table->string('email')->comment("Email");
            $table->string('address')->nullable()->comment("Địa chỉ");
            $table->string('username')->comment("Tên đăng nhập");
            $table->text('password')->comment("Mật khẩu");
            $table->uuid('cityId')->nullable()->comment("Id thành phố");
            $table->tinyInteger('status')->default(1)->comment("Trạng thái");
            $table->string('cmnd')->nullable()->comment("Chứng minh nhân dân");
            $table->date('birthday')->nullable()->comment("Sinh nhật");
            $table->text('images')->nullable()->comment("Ảnh thẻ");
            $table->tinyInteger('isDeleted')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('name');
            $table->index('phone');
            $table->index('email');
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
