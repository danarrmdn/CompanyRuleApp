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
            $table->foreignId('controller_3_id')->nullable()->constrained('users')->after('controller_2_id');
            $table->foreignId('controller_4_id')->nullable()->constrained('users')->after('controller_3_id');
            $table->foreignId('controller_5_id')->nullable()->constrained('users')->after('controller_4_id');
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
