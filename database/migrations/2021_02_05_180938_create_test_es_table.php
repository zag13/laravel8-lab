<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestESTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_es', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('gender')->comment('性别');
            $table->string('phone')->comment('手机');
            $table->string('email')->comment('邮箱');
            $table->string('country')->comment('国家');
            $table->string('address')->comment('地址');
            $table->string('bank')->comment('银行');
            $table->string('company')->comment('公司');
            $table->string('sentence')->comment('句子');
            $table->string('paragraph')->comment('段落');
            $table->text('text')->comment('文章');
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
        Schema::table('test_articles', function (Blueprint $table) {
            //
        });
    }
}
