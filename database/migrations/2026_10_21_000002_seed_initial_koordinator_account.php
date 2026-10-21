<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\KoordinatorSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        (new KoordinatorSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to leave empty
    }
};
