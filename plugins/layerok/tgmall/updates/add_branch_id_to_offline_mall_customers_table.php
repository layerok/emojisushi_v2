<?php namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddBranchIdToCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_customers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('branch_id')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_customers', function (Blueprint $table) {
            $table->dropColumn(['tg_username', 'tg_chat_id', 'tg_address', 'tg_phone']);
        });
    }
}
