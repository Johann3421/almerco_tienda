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
        Schema::table('filter_items', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('name');
            $table->string('file')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filter_items', function (Blueprint $table) {
            $table->dropColumn('file_path');
            $table->dropColumn('file');
        });
    }
};
