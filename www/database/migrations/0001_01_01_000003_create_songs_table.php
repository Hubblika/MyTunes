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
            $table->string('artist');
            $table->string('album'); // ->foreignId('user_id')?
            $table->string('url');
            $table->string('cover_url');
            $table->date('date');
            $table->unsignedMediumInteger('duration');
            $table->string('genre', 60)->nullable();
            $table->timestamps();
        });

        Schema::create('user_likes', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('song_id')->constrained('songs', 'uuid')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
