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
            $table->integer('version')->default(1)->after('status');
            $table->boolean('is_obsolete')->default(false)->after('version');
            $table->foreignId('previous_version_id')->nullable()->after('is_obsolete')->constrained('company_rules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_rules', function (Blueprint $table) {
            $table->dropForeign(['previous_version_id']);
            $table->dropColumn('previous_version_id');
            $table->dropColumn('is_obsolete');
            $table->dropColumn('version');
        });
    }
};
