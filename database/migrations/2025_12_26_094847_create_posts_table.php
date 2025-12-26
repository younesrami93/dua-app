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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // 1. Relationships
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories');

            // 2. The Content
            $table->text('content');
            $table->boolean('is_anonymous')->default(false);

            // 3. Status Management
            // 'published': Visible
            // 'banned': Hidden by Admin/AI
            // 'deleted_by_user': Soft deleted
            $table->enum('status', ['published', 'pending', 'banned', 'deleted_by_user'])->default('published');

            // 4. AI & Safety
            $table->boolean('is_ai_checked')->default(false);
            $table->float('hate_speech_score')->default(0.0); // 0.0 (Safe) -> 1.0 (Toxic)
            $table->string('safety_label')->nullable();

            // 5. Counters (For Speed)
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->unsignedBigInteger('reports_count')->default(0);

            // 6. Meta Data
            $table->json('device_info')->nullable();
            $table->timestamp('banned_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
