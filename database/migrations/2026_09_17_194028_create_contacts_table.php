<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            // Maps to the "Project Description" field submitted by the frontend's contact form.
            $table->text('message');
            // The frontend's contact form submits the same Project Type / Budget Range
            // selects as the quote form. Kept nullable here so future non-project
            // enquiries (e.g. an admin-created contact) are not forced to supply them.
            $table->string('project_type')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('status')->default('new');
            $table->string('source')->default('website');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
