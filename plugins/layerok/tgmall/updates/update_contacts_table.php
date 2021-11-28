<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateContactsTable extends Migration
{
    public function up()
    {
        Schema::table('layerok_tgmall_contacts', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('username')->nullable();
        });
    }

    public function down()
    {
        Schema::table('layerok_tgmall_contacts', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'username']);
        });
    }
}
