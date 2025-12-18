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
        Schema::table('items', function (Blueprint $table) {
            // Clean up legacy category_id (drop FK first, then column) if it exists
            if (Schema::hasColumn('items', 'category_id')) {
                // Safely drop foreign key if present
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Throwable $e) {
                    // Ignore if FK name is different or already dropped
                }

                try {
                    $table->dropColumn('category_id');
                } catch (\Throwable $e) {
                    // Ignore if column is already dropped
                }
            }

            // Basic attribute for item type/category label
            if (!Schema::hasColumn('items', 'type')) {
                $table->string('type')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};


