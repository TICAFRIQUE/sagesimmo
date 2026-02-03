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
        // Ajouter les champs de commission aux échéances
        Schema::table('echeances', function (Blueprint $table) {
            $table->decimal('commission_agence', 10, 2)->nullable()->after('montant_paye')
                  ->comment('Commission de l\'agence prélevée sur cette échéance');
        });

        // Ajouter les champs de commission aux paiements
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('commission_agence', 10, 2)->nullable()->after('montant')
                  ->comment('Commission de l\'agence prélevée sur ce paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echeances_and_paiements', function (Blueprint $table) {
            $table->dropColumn('commission_agence');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn('commission_agence');
        });
    }
};
