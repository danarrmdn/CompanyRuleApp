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
            $table->foreignId('sent_back_by_id')->nullable()->constrained('users')->after('approver_3_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_rules', function (Blueprint $table) {
            $table->dropForeign(['sent_back_by_id']);
            $table->dropColumn('sent_back_by_id');
        });
    }
};
