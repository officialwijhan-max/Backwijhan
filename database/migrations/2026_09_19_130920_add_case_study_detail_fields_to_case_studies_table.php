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
        Schema::table('case_studies', function (Blueprint $table) {
            $table->string('industry_en')->nullable()->after('work_category_id');
            $table->string('industry_ar')->nullable()->after('industry_en');
            $table->string('client_name')->nullable()->after('title_ar');
            $table->boolean('client_visibility')->default(true)->after('client_name');
            $table->string('logo')->nullable()->after('cover_image_url');
            $table->string('headline_en')->nullable()->after('title_ar');
            $table->string('headline_ar')->nullable()->after('headline_en');
            $table->text('hero_description_en')->nullable()->after('summary_ar');
            $table->text('hero_description_ar')->nullable()->after('hero_description_en');
            $table->boolean('featured')->default(false)->after('is_published');
            $table->unsignedSmallInteger('display_order')->default(0)->after('featured');

            $table->index(['featured', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            $table->dropIndex(['featured', 'display_order']);
            $table->dropColumn([
                'industry_en',
                'industry_ar',
                'client_name',
                'client_visibility',
                'logo',
                'headline_en',
                'headline_ar',
                'hero_description_en',
                'hero_description_ar',
                'featured',
                'display_order',
            ]);
        });
    }
};
