<?php

namespace Layerok\TgMall\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCartMessageTable extends Migration
{
    public function up()
    {
        Schema::create('layerok_tgmall_messages', function ($table) {
            $table->increments('id');
            $table->integer('chat_id')->length(50)->nullable();
            $table->integer('msg_id')->length(50)->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('layerok_tgmall_messages');
    }
}