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
        Schema::create('playlists', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('public')->default(true);
            $table->boolean('is_album')->default(false);
            $table->timestamps();
        });

        Schema::create('playlist_collaborators', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignUuid('playlist_uuid')->constrained('playlists', 'uuid')->cascadeOnDelete();
        });

        Schema::create('ordered_songs', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignUuid('playlist_uuid')->constrained('playlists', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('song_uuid')->constrained('songs', 'uuid')->cascadeOnDelete();
            $table->unsignedMediumInteger('index');
            $table->timestamps();

            $table->unique(['playlist_uuid', 'song_uuid']);
            $table->unique(['playlist_uuid', 'index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('playlist_collaborators');
        Schema::dropIfExists('ordered_songs');
    }
};
