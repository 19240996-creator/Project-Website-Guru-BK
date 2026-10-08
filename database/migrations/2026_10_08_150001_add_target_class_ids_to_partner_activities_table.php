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
        Schema::table('partner_activities', function (Blueprint $table) {
            $table->json('target_class_ids')->nullable()->after('target_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_activities', function (Blueprint $table) {
            $table->dropColumn('target_class_ids');
        });
    }
};
