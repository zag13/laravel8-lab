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
            $table->string('occupation')->default('')->comment('职业');
            $table->string('phone')->default('')->comment('手机');
            $table->string('email')->default('')->comment('邮箱');
            $table->string('country')->default('')->comment('国家');
            $table->string('address')->default('')->comment('地址');
            $table->string('bank')->default('')->comment('银行');
            $table->string('company')->default('')->comment('公司');
            $table->string('sentence')->default('')->comment('句子');
            $table->mediumText('paragraph')->nullable()->comment('段落');
            $table->text('text')->nullable()->comment('文章');
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
