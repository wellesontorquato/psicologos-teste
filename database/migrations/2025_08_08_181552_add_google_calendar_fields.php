<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campos no users
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_access_token')->nullable()->after('remember_token');
            $table->text('google_refresh_token')->nullable()->after('google_access_token');
            $table->timestamp('google_token_expires_at')->nullable()->after('google_refresh_token');
            $table->string('google_calendar_id')->nullable()->after('google_token_expires_at');
            $table->boolean('google_connected')->default(false)->after('google_calendar_id');
        });

        // Campos no sessoes
        Schema::table('sessoes', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->index()->after('id');
            $table->string('google_sync_status')->default('pending')->after('google_event_id'); // pending|ok|error
            $table->text('google_sync_error')->nullable()->after('google_sync_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at',
                'google_calendar_id',
                'google_connected',
            ]);
        });

        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropColumn([
                'google_event_id',
                'google_sync_status',
                'google_sync_error',
            ]);
        });
    }
};
