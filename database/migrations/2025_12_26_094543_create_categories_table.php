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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Content
            $table->string('name'); // e.g., "Health", "Parents"
            $table->string('slug')->unique(); // e.g., "health-and-wellness"
            $table->string('icon_url')->nullable();

            // Admin Controls
            $table->boolean('is_active')->default(true); // Helper to hide without deleting
            $table->integer('order')->default(0); // For sorting in the app

            $table->softDeletes(); // <--- Soft Delete Column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
