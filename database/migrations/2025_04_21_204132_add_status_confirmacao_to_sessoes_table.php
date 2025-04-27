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
            $table->enum('status_confirmacao', ['PENDENTE', 'CONFIRMADA', 'CANCELADA', 'REMARCAR'])->default('PENDENTE');
        });
    }

    public function down()
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropColumn('status_confirmacao');
        });
    }
};
