<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('evolucoes', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('texto');
        });
    }

    public function down()
    {
        Schema::table('evolucoes', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
