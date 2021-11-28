<?php

namespace Layerok\TgMall\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateContactsTable extends Migration
{
    public function up()
    {
        Schema::create('layerok_tgmall_contacts', function ($table) {
            $table->increments('id');
            $table->integer('chat_id')->length(50)->nullable();
            $table->string('name')->nullable();
            $table->integer('point_id')->length(11)->default(0);
            $table->text('address')->nullable();
            $table->string('telephone')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('layerok_tgmall_contacts');
    }
}
