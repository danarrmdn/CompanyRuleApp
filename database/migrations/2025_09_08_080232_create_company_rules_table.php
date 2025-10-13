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
        Schema::create('company_rules', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('number');
            $table->string('document_name');
            $table->text('reason_of_revision');
            $table->date('effective_date');
            $table->string('file_path')->nullable();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('controller_1_id')->nullable()->constrained('users');
            $table->foreignId('controller_2_id')->nullable()->constrained('users');
            $table->json('approver_ids');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_rules');
    }
};
