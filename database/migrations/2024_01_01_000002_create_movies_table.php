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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis')->nullable();
            $table->json('genre')->nullable(); // stored as JSON array e.g. ["Action", "Sci-Fi"]
            $table->integer('duration_minutes');
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->string('poster_url')->nullable();
            $table->string('trailer_url')->nullable();
            $table->date('release_date')->nullable();
            $table->string('status')->default('now_showing'); // now_showing, coming_soon, archived
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('release_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};

