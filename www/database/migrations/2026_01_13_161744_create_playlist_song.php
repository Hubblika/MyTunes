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
        Schema::create('playlist_songs', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('playlist_id')->references('uuid')->on('playlists')->onDelete('cascade');
            $table->foreignUuid('song_id')->references('uuid')->on('songs')->onDelete('cascade');
            $table->integer('position')->nullable();
            $table->timestamps();

            $table->unique(['playlist_id', 'song_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_songs');
    }
};
