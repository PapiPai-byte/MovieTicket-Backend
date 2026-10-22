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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete();
            $table->foreignId('screen_id')->constrained('screens')->cascadeOnDelete();
            $table->timestampTz('starts_at');
            $table->decimal('price', 8, 2);
            $table->string('status')->default('active'); // active, cancelled, completed
            $table->timestamps();
            $table->softDeletes();

            $table->index(['movie_id', 'starts_at']);
            $table->index(['screen_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};

