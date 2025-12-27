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
        Schema::table('app_users', function (Blueprint $table) {
            // 1. Drop the existing UNIQUE constraint
            // Laravel automatically guesses the index name: 'app_users_device_uuid_unique'
            $table->dropUnique(['device_uuid']);
            // 2. Add a standard INDEX (for performance)
            $table->index('device_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            // Revert: Drop index and make it unique again
            $table->dropIndex(['device_uuid']);
            $table->unique('device_uuid');
        });
    }
};
