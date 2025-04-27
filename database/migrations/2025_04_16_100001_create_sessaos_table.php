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
    Schema::create('sessaos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('paciente_id');
        $table->dateTime('data_hora');
        $table->integer('duracao')->default(50); // em minutos
        $table->decimal('valor', 8, 2)->nullable();
        $table->boolean('foi_pago')->default(false);
        $table->text('observacoes')->nullable();
        $table->timestamps();

        $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessaos');
    }
};
