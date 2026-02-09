<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColorFromItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('color')->nullable();
        });
    }
}
