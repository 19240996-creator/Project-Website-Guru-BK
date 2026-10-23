<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\KepalaKampusAdminDicoSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        (new KepalaKampusAdminDicoSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User::whereIn('username', ['kepalakampus', 'admindico'])->delete();
    }
};
