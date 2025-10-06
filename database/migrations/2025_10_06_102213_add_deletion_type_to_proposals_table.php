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
        Schema::table('proposals', function (Blueprint $table) {
            $table->enum('deletion_type', ['admin', 'bd', null])->nullable()->after('deleted_at')
                ->comment('Tracks who deleted the proposal: admin (hidden from all), bd (hidden from bd only), null (not deleted)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('deletion_type');
        });
    }
};
