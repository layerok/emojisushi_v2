<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class AddFileIdToSystemFilesTable extends Migration
{
    public function up()
    {
        Schema::table('system_files', function (Blueprint $table) {
            $table->string('file_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('system_files', function (Blueprint $table) {
            $table->dropColumn(['file_id']);
        });
    }
}
