<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('download_log', function (Blueprint $table) {
            $table->id();
            $table->string('class_name')->comment('类名');
            $table->string('action_name')->comment('操作名');
            $table->text('params')->nullable()->comment('序列化参数');
            $table->string('file_name')->nullable()->comment('文件名');
            $table->string('file_size')->nullable()->comment('文件大小');
            $table->string('file_link')->nullable()->comment('文件下载地址');
            $table->bigInteger('creator_id')->comment('创建人ID');
            $table->string('creator_name')->comment('创建人姓名');
            $table->tinyInteger('status')->default('1')->comment('0:下载成功 1:准备下载 2:下载中 3:下载异常 4:用户已经点下载 5:不展示');
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
        Schema::dropIfExists('download_log');
    }
}
