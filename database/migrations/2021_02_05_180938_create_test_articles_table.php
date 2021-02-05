<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_articles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('title')->comment('文章标题');
            $table->text('content')->comment('文章内容');
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
