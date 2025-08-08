<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Preenche user_id com base no paciente
        DB::table('sessoes')->update([
            'user_id' => DB::raw('(SELECT user_id FROM pacientes WHERE pacientes.id = sessoes.paciente_id)')
        ]);

        // Torna a coluna obrigatória e adiciona a chave estrangeira
        Schema::table('sessoes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            // $table->dropColumn('user_id'); // Descomente se quiser excluir a coluna
        });
    }
};
