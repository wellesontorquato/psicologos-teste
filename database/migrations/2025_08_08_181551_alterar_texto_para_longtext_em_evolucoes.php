<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterarTextoParaLongtextEmEvolucoes extends Migration
{
    public function up()
    {
        Schema::table('evolucoes', function (Blueprint $table) {
            $table->longText('texto')->change();
        });
    }

    public function down()
    {
        Schema::table('evolucoes', function (Blueprint $table) {
            $table->text('texto')->change(); // ou o tipo anterior, se necessário
        });
    }
}

