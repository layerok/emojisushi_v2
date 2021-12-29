<?php

namespace Lovata\BaseCode\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * some_upgrade_file.php
 */
class CreateHideCategoriesInBranchTable extends Migration
{
    ///
    public function up()
    {
        Schema::create('lovata_basecode_hide_categories_in_branch', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('branch_id')->unsigned();

            $table->foreign('branch_id')
                ->references('id')
                ->on('lovata_basecode_branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('offline_mall_categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('lovata_basecode_hide_categories_in_branch');
    }
}


