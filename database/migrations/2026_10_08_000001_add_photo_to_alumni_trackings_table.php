<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni_trackings', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('notes');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('alumni_trackings', function (Blueprint $table) {
            $table->dropColumn('photo');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
