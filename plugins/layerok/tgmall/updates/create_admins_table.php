<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('layerok_tgmall_admins', function ($table) {
            $table->increments('id');
            $table->integer('chat_id')->length(50);
            $table->integer('point_id')->length(11);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('layerok_tgmall_admins');
    }
}
