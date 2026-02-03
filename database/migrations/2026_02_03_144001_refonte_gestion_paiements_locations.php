<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter colonnes à la table paiements
        Schema::table('paiements', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements', 'type_paiement')) {
                $table->enum('type_paiement', ['loyer', 'caution', 'avance', 'frais_agence'])->default('loyer')->after('payable_type');
            }
            if (!Schema::hasColumn('paiements', 'statut')) {
                $table->enum('statut', ['en_attente', 'paye', 'partiel', 'annule'])->default('paye')->after('type_paiement');
            }
            if (!Schema::hasColumn('paiements', 'echeance_id')) {
                $table->foreignId('echeance_id')->nullable()->after('id')->constrained('echeances')->onDelete('set null');
            }
        });

        // Ajouter colonnes à la table locations
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'avance_sur_loyer')) {
                $table->integer('avance_sur_loyer')->default(0)->comment('Nombre de mois d\'avance')->after('loyer_mensuel');
            }
            if (!Schema::hasColumn('locations', 'montant_avance')) {
                $table->decimal('montant_avance', 15, 2)->default(0)->after('avance_sur_loyer');
            }
            if (!Schema::hasColumn('locations', 'premier_paiement_valide')) {
                $table->boolean('premier_paiement_valide')->default(false)->after('montant_avance');
            }
        });

        // Modifier la colonne statut dans echeances
        // D'abord étendre l'ENUM pour inclure les nouvelles valeurs
        DB::statement("ALTER TABLE echeances MODIFY COLUMN statut ENUM('en_attente', 'payee', 'en_retard', 'a_echeance', 'partiel', 'paye', 'impaye', 'cloture') DEFAULT 'en_attente'");
        
        // Ensuite migrer les anciennes valeurs
        DB::statement("UPDATE echeances SET statut = 'paye' WHERE statut = 'payee'");
        DB::statement("UPDATE echeances SET statut = 'a_echeance' WHERE statut = 'en_attente'");
        
        // Enfin, nettoyer l'ENUM
        DB::statement("ALTER TABLE echeances MODIFY COLUMN statut ENUM('a_echeance', 'en_retard', 'partiel', 'paye', 'impaye', 'cloture') DEFAULT 'a_echeance'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['echeance_id']);
            $table->dropColumn(['type_paiement', 'statut', 'echeance_id']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['avance_sur_loyer', 'montant_avance', 'premier_paiement_valide']);
        });

        Schema::table('echeances', function (Blueprint $table) {
            $table->enum('statut', ['en_attente', 'payee', 'en_retard'])->default('en_attente')->change();
        });
    }
};
