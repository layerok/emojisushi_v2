<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateActionsTable extends Migration
{
    public function up()
    {
        Schema::create('layerok_tgmall_actions', function ($table) {
            $table->increments('id');
            $table->integer('chat_id')->length(50);
            $table->integer('action_id')->length(50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('layerok_tgmall_actions');
    }
}
