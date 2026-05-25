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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->onDelete('cascade');
            $table->string('title');
            
            // Abstract Stage
            $table->text('abstract_text')->nullable();
            $table->string('abstract_file_path')->nullable();
            $table->boolean('is_abstract_paid')->default(false);
            $table->enum('abstract_status', ['pending', 'approved', 'denied'])->default('pending');
            $table->text('abstract_rejection_reason')->nullable();
            
            // Full Paper Stage
            $table->string('full_paper_file_path')->nullable();
            $table->boolean('is_full_paper_paid')->default(false);
            $table->enum('full_paper_status', ['pending', 'approved', 'denied'])->default('pending');
            $table->text('full_paper_rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
