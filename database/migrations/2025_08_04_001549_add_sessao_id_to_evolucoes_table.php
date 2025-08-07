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
            $table->foreignId('sessao_id')
                ->nullable()
                ->after('paciente_id')
                ->constrained('sessoes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('evolucoes', function (Blueprint $table) {
            $table->dropForeign(['sessao_id']);
            $table->dropColumn('sessao_id');
        });
    }
};
