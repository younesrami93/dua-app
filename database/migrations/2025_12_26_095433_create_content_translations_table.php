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
        Schema::create('content_translations', function (Blueprint $table) {
            $table->id();

            // 1. What is being translated? (Post or Comment)
            $table->morphs('translatable');

            // 2. The Language and Content
            $table->string('locale', 10); // e.g. 'en', 'fr', 'ar'
            $table->text('content'); // The translated text

            $table->timestamps();
            $table->softDeletes();

            // 3. Constraint
            // Prevent duplicate translations for the same language on the same item
            $table->unique(['translatable_id', 'translatable_type', 'locale'], 'trans_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_translations');
    }
};
