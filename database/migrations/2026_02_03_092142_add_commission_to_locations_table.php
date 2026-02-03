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
        Schema::table('locations', function (Blueprint $table) {
            $table->decimal('commission_agence', 10, 2)->nullable()->after('montant_frais_agence')
                  ->comment('Commission de l\'agence (pourcentage ou montant fixe)');
            $table->enum('type_commission', ['pourcentage', 'montant'])->nullable()->after('commission_agence')
                  ->comment('Type de commission: pourcentage du loyer ou montant fixe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['commission_agence', 'type_commission']);
        });
    }
};
