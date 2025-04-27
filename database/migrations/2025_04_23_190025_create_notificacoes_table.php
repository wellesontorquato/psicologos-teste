<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('mensagem')->nullable();
            $table->string('tipo');
            $table->boolean('lida')->default(false);
            $table->timestamp('visto_em')->nullable();
            $table->nullableMorphs('relacionado'); // relacionado_id + relacionado_type
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notificacoes');
    }
};
