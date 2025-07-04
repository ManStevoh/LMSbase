<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('age')->nullable();
            $table->string('msme_sector')->nullable();
            $table->string('nationality')->nullable();
            $table->string('lga')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
        //drop columns
            $table->dropColumn('age');
            $table->dropColumn('msme_sector');
            $table->dropColumn('nationality');
            $table->dropColumn('lga');
        });
    }
};
