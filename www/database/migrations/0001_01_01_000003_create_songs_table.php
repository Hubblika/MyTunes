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
            $table->date('created_at')->useCurrent();
            $table->unsignedMediumInteger('duration');
            $table->boolean('is_explicit');
            $table->string('genre', 60)->nullable();
        });

        Schema::create('_user_songs', function (Blueprint $table) {
            $table->foreignUuid('song_uuid')->constrained('songs', 'uuid')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        });

        Schema::create('_user_likes', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignUuid('song_uuid')->constrained('songs', 'uuid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
        Schema::dropIfExists('_user_songs');
        Schema::dropIfExists('_user_likes');
    }
};
