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
        Schema::table('conferences', function (Blueprint $table) {
            $table->decimal('abstract_fee', 10, 2)->default(0.00)->after('conference_material_fee');
            $table->decimal('full_paper_fee', 10, 2)->default(0.00)->after('abstract_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn(['abstract_fee', 'full_paper_fee']);
        });
    }
};
