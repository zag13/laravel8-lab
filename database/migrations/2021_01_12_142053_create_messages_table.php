<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('message')->comment('消息内容');
            $table->integer('group_id')->unsigned()->default('0')->comment('0:公共频道');
            $table->bigInteger('creator_id')->unsigned()->comment('发送人ID');
            $table->tinyInteger('status')->default('0')->comment('0:正常1:已撤回2:已删除');
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
        Schema::dropIfExists('message');
    }
}
