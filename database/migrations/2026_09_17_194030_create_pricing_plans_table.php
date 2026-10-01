<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('number', 4);
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('tag_en');
            $table->string('tag_ar');
            $table->string('price_en');
            $table->string('price_ar');
            $table->string('price_note_en');
            $table->string('price_note_ar');
            $table->text('summary_en');
            $table->text('summary_ar');
            $table->json('includes_en');
            $table->json('includes_ar');
            $table->text('best_for_en');
            $table->text('best_for_ar');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
