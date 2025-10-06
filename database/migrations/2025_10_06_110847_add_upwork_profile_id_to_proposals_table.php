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
            $table->foreignId('upwork_profile_id')->nullable()->after('user_id')
                ->constrained('upwork_profiles')->onDelete('set null')
                ->comment('Associated Upwork profile for this proposal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['upwork_profile_id']);
            $table->dropColumn('upwork_profile_id');
        });
    }
};
