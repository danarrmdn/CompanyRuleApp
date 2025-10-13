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
            $table->dropColumn('approver_ids');

            $table->foreignId('approver_1_id')->nullable()->constrained('users');
            $table->foreignId('approver_2_id')->nullable()->constrained('users');
            $table->foreignId('approver_3_id')->nullable()->constrained('users');
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
