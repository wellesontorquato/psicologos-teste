<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_inbox', function (Blueprint $table) {
            $table->id();

            $table->string('source', 50);
            $table->string('message_key', 191)->unique();
            $table->string('request_id', 36)->nullable();

            $table->string('event', 50)->nullable();
            $table->string('from', 80)->nullable();
            $table->text('body')->nullable();

            $table->enum('status', ['RECEIVED','PROCESSING','PROCESSED','FAILED'])->default('RECEIVED');
            $table->unsignedSmallInteger('attempts')->default(0);

            $table->longText('payload_json')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_inbox');
    }
};
