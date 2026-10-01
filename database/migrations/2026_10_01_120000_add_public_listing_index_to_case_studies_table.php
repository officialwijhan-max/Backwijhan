<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Covers the public /work listing query:
     * WHERE is_published = 1 ORDER BY featured DESC, display_order, published_at DESC.
     */
    public function up(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            $table->index(
                ['is_published', 'featured', 'display_order', 'published_at'],
                'case_studies_public_listing_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            $table->dropIndex('case_studies_public_listing_index');
        });
    }
};
