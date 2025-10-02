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
        Schema::create('upwork_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_name');
            $table->string('country');
            $table->text('username'); // Will be encrypted in the model
            $table->text('password'); // Will be encrypted in the model
            $table->json('assigned_bd_ids')->nullable(); // JSON array of BD user IDs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upwork_profiles');
    }
};
