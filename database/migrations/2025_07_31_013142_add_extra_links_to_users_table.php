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
        Schema::table('users', function (Blueprint $table) {
            // adiciona apenas os novos campos
            $table->string('link_extra1')->nullable()->after('link_principal');
            $table->string('link_extra2')->nullable()->after('link_extra1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['link_extra1', 'link_extra2']);
        });
    }
};
