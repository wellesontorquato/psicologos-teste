<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf', 14)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'crp')) {
                $table->string('crp', 20)->nullable()->after('cpf');
            }
            if (!Schema::hasColumn('users', 'data_nascimento')) {
                $table->date('data_nascimento')->nullable()->after('crp');
            }
        });

        // Corrige dados em branco que causam conflitos com índices únicos
        DB::statement("UPDATE users SET cpf = NULL WHERE cpf = ''");
        DB::statement("UPDATE users SET crp = NULL WHERE crp = ''");

        // Adiciona os índices únicos
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cpf')) return;
            if (!Schema::hasColumn('users', 'crp')) return;

            $table->unique('cpf');
            $table->unique('crp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropUnique(['crp']);
            $table->dropColumn(['cpf', 'crp', 'data_nascimento']);
        });
    }
};
