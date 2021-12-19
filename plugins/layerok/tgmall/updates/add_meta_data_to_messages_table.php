<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class AddMetaDataTable extends Migration
{
    public function up()
    {
        Schema::table('layerok_tgmall_messages', function (Blueprint $table) {
            $table->json('meta_data')->nullable();
        });
    }

    public function down()
    {
        Schema::table('layerok_tgmall_messages', function (Blueprint $table) {
            $table->dropColumn(['meta_data']);
        });
    }
}
