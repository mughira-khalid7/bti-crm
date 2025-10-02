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
            // Change status enum to include 'copied'
            $table->dropColumn('status');
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'interviewing', 'copied', 'deleted', 'viewed', 'meeting_scheduled', 'phone_shared'])->default('submitted')->after('notes');
            $table->boolean('is_copy')->default(false)->after('status');
            $table->foreignId('original_proposal_id')->nullable()->constrained('proposals')->onDelete('cascade')->after('is_copy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (Schema::hasColumn('proposals', 'original_proposal_id')) {
                $table->dropForeign(['original_proposal_id']);
            }
            if (Schema::hasColumn('proposals', 'is_copy')) {
                $table->dropColumn(['is_copy', 'original_proposal_id']);
            }
            $table->dropColumn('status');
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'interviewing'])->default('submitted');
        });
    }
};
