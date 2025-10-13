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
        Schema::table('company_rule_logs', function (Blueprint $table) {
            $table->dropForeign(['company_rule_id']);
            $table->foreign('company_rule_id')
                  ->references('id')->on('company_rules')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_rule_logs', function (Blueprint $table) {
            $table->dropForeign(['company_rule_id']);
            $table->foreign('company_rule_id')
                  ->references('id')->on('company_rules')
                  ->onDelete('cascade');
        });
    }
};