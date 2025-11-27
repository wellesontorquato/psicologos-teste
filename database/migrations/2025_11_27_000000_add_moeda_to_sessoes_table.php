<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            // 3 letras, ex: BRL, USD, EUR...
            $table->char('moeda', 3)
                  ->default('BRL')
                  ->after('valor');
        });
    }

    public function down(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropColumn('moeda');
        });
    }
};
