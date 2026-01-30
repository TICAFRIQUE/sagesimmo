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
        Schema::table('demande_interets', function (Blueprint $table) {
            $table->enum('type_commission', ['fixe', 'pourcentage'])->nullable()->after('commission_agence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_interets', function (Blueprint $table) {
            $table->dropColumn('type_commission');
        });
    }
};
