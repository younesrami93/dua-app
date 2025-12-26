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
        Schema::create('content_reports', function (Blueprint $table) {
            $table->id();

            // 1. Who is reporting?
            $table->foreignId('reporter_id')->constrained('app_users')->onDelete('cascade');

            // 2. What are they reporting? (Polymorphic)
            // This creates two columns: 'reported_type' (Post/Comment) and 'reported_id'
            $table->morphs('reported');

            // 3. Details
            $table->string('reason'); // e.g. 'hate_speech', 'spam', 'harassment'
            $table->text('details')->nullable(); // Optional user description

            // 4. Admin Management
            $table->enum('status', ['open', 'resolved', 'dismissed'])->default('open');
            $table->text('admin_notes')->nullable();
            $table->softDeletes(); // <--- Soft Delete Column

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_reports');
    }
};
