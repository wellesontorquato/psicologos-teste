<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receita_saude_recibos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->foreignId('sessao_id')->nullable()->unique()->constrained('sessoes')->nullOnDelete();

            $table->date('data_pagamento');
            $table->date('data_atendimento')->nullable();
            $table->string('codigo_rendimento', 20)->default('R01.001.001');
            $table->string('codigo_ocupacao', 3);
            $table->decimal('valor_pagamento', 13, 2);
            $table->decimal('valor_deducao', 13, 2)->nullable();
            $table->string('descricao', 255)->nullable();
            $table->string('recebido_de', 2)->default('PF');
            $table->string('cpf_pagador', 11);
            $table->string('cpf_beneficiario', 11);
            $table->string('indicador_cpf_nao_informado', 1)->nullable();
            $table->string('cnpj', 14)->nullable();
            $table->string('indicador_irrf', 1)->nullable();
            $table->decimal('valor_irrf', 13, 2)->nullable();
            $table->string('indicador_recibo', 1)->default('S');
            $table->string('cpf_profissional', 11);
            $table->string('registro_profissional', 15)->nullable();

            $table->string('numero_recibo', 80)->nullable();
            $table->enum('status', ['rascunho', 'exportado', 'emitido', 'cancelado'])->default('rascunho');
            $table->timestamp('exportado_em')->nullable();
            $table->timestamp('emitido_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'data_pagamento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receita_saude_recibos');
    }
};
