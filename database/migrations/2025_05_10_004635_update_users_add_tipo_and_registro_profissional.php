<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove a coluna antiga 'crp'
            if (Schema::hasColumn('users', 'crp')) {
                $table->dropColumn('crp');
            }

            // Adiciona as novas colunas
            $table->string('tipo_profissional')->after('cpf');
            $table->string('registro_profissional')->nullable()->after('tipo_profissional');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverte: remove as novas colunas
            $table->dropColumn(['tipo_profissional', 'registro_profissional']);

            // Opcional: recria a coluna 'crp' se quiser reverter
            $table->string('crp')->nullable();
        });
    }
};
