<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();

            // Usuário que realizou a ação (pode ser null se for um visitante)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Nome da ação (ex: login, updated_profile, deleted_patient)
            $table->string('action');

            // Tipo do modelo envolvido na ação (ex: App\Models\Paciente)
            $table->string('model_type')->nullable();

            // ID do modelo envolvido
            $table->unsignedBigInteger('model_id')->nullable();

            // Descrição da ação
            $table->text('description')->nullable();

            // IP do usuário que executou
            $table->ipAddress('ip_address')->nullable();

            // Navegador do usuário (User-Agent)
            $table->text('user_agent')->nullable();

            // Horários
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
