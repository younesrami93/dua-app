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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // 1. Relationships
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');

            // The "Reply" Logic: A comment can belong to another comment
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();

            // 2. Content
            $table->text('content');

            // 3. Status
            // 'visible': Normal
            // 'hidden': Collapsed due to low score or user preference
            // 'banned': Removed by Admin/AI
            $table->enum('status', ['visible', 'hidden', 'banned'])->default('visible');

            // 4. Safety
            $table->float('hate_speech_score')->default(0.0);
            $table->boolean('is_ai_checked')->default(false);

            // 5. Meta & Stats
            $table->json('device_info')->nullable();
            $table->unsignedBigInteger('reports_count')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
