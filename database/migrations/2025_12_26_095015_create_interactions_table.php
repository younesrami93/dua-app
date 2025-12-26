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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();

            // 1. Who and What
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');

            // 2. The Type of Action
            // 'like': User liked the post
            // 'share': User shared the post
            $table->enum('type', ['like', 'share']);

            // 3. Timestamps
            $table->timestamps();
            $table->softDeletes(); // <--- Soft Delete Column

            // 4. Constraint (Crucial)
            // Ensure a user can only 'like' a specific post ONCE.
            // They can 'share' multiple times if you want, but usually, we limit that too.
            // For now, let's limit both to unique actions per post.
            $table->unique(['app_user_id', 'post_id', 'type']);

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
