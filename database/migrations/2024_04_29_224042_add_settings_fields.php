<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
            $table->string('file_path')->nullable()->after('value');
            $table->string('file')->nullable()->after('file_path');
            $table->uuid('token')->nullable()->after('file');
            $table->string('url')->nullable()->after('token');
            $table->boolean('active')->default(true)->after('url');
        });

        DB::table('settings')->insert([
            [
                'key' => 'img_superior',
                'active' => false,
            ],
            [
                'key' => 'img_medio',
                'active' => false,
            ],
            [
                'key' => 'img_medio_mobile',
                'active' => false,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('value');
            $table->dropColumn('file_path');
            $table->dropColumn('file');
            $table->dropColumn('token');
            $table->dropColumn('url');
            $table->dropColumn('active');
        });
    }
};
