<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class AlterOfflineMallCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_customers', function (Blueprint $table) {
            $table->string('tg_username')->nullable();
            $table->integer('tg_chat_id')->length(50)->nullable();
            $table->text('tg_address')->nullable();
            $table->string('tg_phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_customers', function (Blueprint $table) {
            $table->dropColumn(['tg_username', 'tg_chat_id', 'tg_address', 'tg_phone']);
        });
    }
}
