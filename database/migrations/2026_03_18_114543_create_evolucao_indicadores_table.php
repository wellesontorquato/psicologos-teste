<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evolucao_indicadores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evolucao_id')
                ->constrained('evolucoes')
                ->cascadeOnDelete();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();

            $table->foreignId('sessao_id')
                ->nullable()
                ->constrained('sessoes')
                ->nullOnDelete();

            $table->string('estado_emocional', 50)->nullable();
            $table->unsignedTinyInteger('intensidade')->nullable();
            $table->unsignedTinyInteger('alerta')->nullable();
            $table->text('observacoes')->nullable();

            $table->timestamps();

            $table->unique('evolucao_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolucao_indicadores');
    }
};