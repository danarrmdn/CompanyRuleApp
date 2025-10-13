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
        Schema::table('company_rules', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
            $table->string('number')->nullable()->change();
            $table->string('document_name')->nullable()->change();
            $table->text('reason_of_revision')->nullable()->change();
            $table->date('effective_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_rules', function (Blueprint $table) {
            //
        });
    }
};
