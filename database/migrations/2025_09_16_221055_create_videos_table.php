<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            // Platform-agnostic identifiers
            $table->string('platform'); // e.g., 'eporner', 'xvideos', etc.
            $table->string('source_id'); // ID from the source platform

            // Human/content fields
            $table->string('title');
            $table->string('source_url')->unique();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->unsignedBigInteger('dislike_count')->default(0);
            $table->decimal('rating', 5, 2)->nullable();
            $table->unsignedInteger('duration')->nullable(); // seconds
            $table->string('embed_url')->nullable();
            $table->string('thumb_url')->nullable();
            $table->unsignedInteger('thumb_width')->nullable();
            $table->unsignedInteger('thumb_height')->nullable();
            $table->json('thumbs')->nullable();
            $table->text('keywords')->nullable();
            $table->boolean('is_vr')->default(false);
            $table->boolean('is_3d')->default(false);
            $table->string('uploader')->nullable(); // channel/uploader/producer if available
            $table->json('tags')->nullable();
            $table->json('categories')->nullable();
            $table->json('performers')->nullable(); // pornstars/actors
            $table->timestamp('uploaded_at')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            // Constraints & indexes
            $table->unique(['platform', 'source_id']);
            $table->index(['views']);
            $table->index(['uploaded_at']);
            $table->index(['platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
