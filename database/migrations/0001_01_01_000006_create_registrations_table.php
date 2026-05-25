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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendee_type_id')->constrained()->onDelete('cascade');
            $table->boolean('wants_accommodation')->default(false);
            $table->boolean('wants_materials')->default(false);
            
            // Financial Status Flags
            $table->boolean('is_attendance_paid')->default(false);
            $table->boolean('is_accommodation_paid')->default(false);
            $table->boolean('is_materials_paid')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
