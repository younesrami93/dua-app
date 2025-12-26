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
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();

            // 1. Identity (Nullable because Guests have none of this)
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable(); // Null for Guest & Social Login
            $table->string('avatar_url')->nullable();

            // 2. Auth Logic
            $table->enum('auth_provider', ['email', 'google', 'facebook', 'apple', 'guest'])->default('guest');
            $table->string('social_id')->nullable()->index(); // The ID from Google/Facebook

            // 3. State & Status
            $table->boolean('is_guest')->default(true);
            $table->enum('status', ['active', 'suspended', 'banned'])->default('active');

            // 4. Bad Behavior Metrics (For auto-banning later)
            $table->integer('hate_speech_violation_count')->default(0);
            $table->integer('banned_posts_count')->default(0);

            // 5. Device Locking (The "Fingerprint")
            // We use this to identify guests AND to ban devices permanently
            $table->string('device_uuid')->unique();
            $table->json('last_device_info')->nullable(); // Store specific model/OS version
            $table->string('country_code')->nullable();
            $table->ipAddress('last_ip_address')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_users');
    }
};
