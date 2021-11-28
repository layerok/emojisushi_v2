<?php

namespace Layerok\TgMall\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('layerok_tgmall_reviews', function ($table) {
            $table->increments('id');
            $table->integer('chat_id')->length(50)->nullable();
            $table->integer('is_active')->length(2);
            $table->string('point_title')->nullable();
            $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('layerok_tgmall_reviews');
    }
}
