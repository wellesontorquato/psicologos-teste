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
        Schema::table('sessoes', function (Blueprint $table) {
            $table->boolean('lembrete_enviado')->default(false);
        });
    }

    public function down()
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropColumn('lembrete_enviado');
        });
    }

};
