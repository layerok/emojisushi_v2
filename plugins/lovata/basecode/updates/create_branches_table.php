<?php

namespace Lovata\BaseCode\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * some_upgrade_file.php
 */
class CreateBranchesTable extends Migration
{
    ///
    public function up()
    {
        Schema::create('lovata_basecode_branches', function ($table) {
            $table->increments('id')->index();
            $table->string('name');
            $table->text('telegram_chat_id')->nullable();
            $table->text('telegram_bot_id')->nullable();
            $table->integer('poster_spot_tablet_id')->nullable();
            $table->text('delivery')->nullable();
            $table->text('phones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('lovata_basecode_branches');
    }
}


