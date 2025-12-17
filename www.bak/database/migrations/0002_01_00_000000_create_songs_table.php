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
        Schema::create('songs', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('title');
            $table->date('created_at');
            $table->unsignedMediumInteger('duration');
            $table->boolean('is_explicit');
        });

        Schema::create('_song_to_user', function (Blueprint $table) {
            $table->foreignUuid('song_uuid')->constrained('songs', 'uuid')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
        Schema::dropIfExists('_song_to_user');
    }
};
