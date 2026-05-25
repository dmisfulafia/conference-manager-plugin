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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            
            // Address, Next of Kin, and Next of Kin Phone (from plan edits)
            $table->string('address')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            
            $table->enum('status', ['ongoing', 'past'])->default('ongoing');
            $table->string('venue');
            $table->decimal('accommodation_fee', 10, 2)->default(0.00);
            $table->decimal('conference_material_fee', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
