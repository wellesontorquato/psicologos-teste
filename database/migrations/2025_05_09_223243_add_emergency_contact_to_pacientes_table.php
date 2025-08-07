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
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('nome_contato_emergencia')->nullable();
            $table->string('telefone_contato_emergencia')->nullable();
            $table->string('parentesco_contato_emergencia')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn([
                'nome_contato_emergencia',
                'telefone_contato_emergencia',
                'parentesco_contato_emergencia',
            ]);
        });
    }
};
