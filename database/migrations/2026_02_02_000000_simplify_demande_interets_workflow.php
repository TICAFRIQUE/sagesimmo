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
        Schema::table('demande_interets', function (Blueprint $table) {
            // Supprimer les colonnes inutiles
            $table->dropColumn([
                'pieces_demandees',
                'pieces_fournies',
                'documents_urls',
                'raison_refus_dossier',
                'date_signature_contrat',
                'commission_agence',
                'type_commission',
                'motif_refus',
            ]);
            
            // Ajouter la nouvelle colonne motif_cloture
            $table->text('motif_cloture')->nullable()->after('date_finalisation');
        });

        // Mettre à jour les anciens statuts vers les nouveaux
        DB::table('demande_interets')->where('statut', 'documents_recus')->update(['statut' => 'visite_effectuee']);
        DB::table('demande_interets')->where('statut', 'dossier_valide')->update(['statut' => 'visite_effectuee']);
        DB::table('demande_interets')->where('statut', 'contrat_genere')->update(['statut' => 'contrat_envoye']);
        DB::table('demande_interets')->whereIn('statut', ['cloture_refus', 'cloture_non_interesse'])->update(['statut' => 'cloture']);

        // Modifier le type ENUM du statut
        DB::statement("ALTER TABLE demande_interets MODIFY COLUMN statut ENUM(
            'nouvelle',
            'contrat_envoye',
            'visite_planifiee',
            'visite_effectuee',
            'paiement_en_attente',
            'paiement_valide',
            'cloture'
        ) DEFAULT 'nouvelle'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer l'ancien ENUM
        DB::statement("ALTER TABLE demande_interets MODIFY COLUMN statut ENUM(
            'nouvelle',
            'visite_planifiee',
            'visite_effectuee',
            'documents_recus',
            'dossier_valide',
            'contrat_genere',
            'paiement_en_attente',
            'paiement_valide',
            'cloture_refus',
            'cloture_non_interesse'
        ) DEFAULT 'nouvelle'");

        Schema::table('demande_interets', function (Blueprint $table) {
            // Supprimer motif_cloture
            $table->dropColumn('motif_cloture');
            
            // Restaurer les colonnes supprimées
            $table->text('pieces_demandees')->nullable();
            $table->text('pieces_fournies')->nullable();
            $table->text('documents_urls')->nullable();
            $table->text('raison_refus_dossier')->nullable();
            $table->dateTime('date_signature_contrat')->nullable();
            $table->decimal('commission_agence', 10, 2)->nullable();
            $table->string('type_commission')->nullable();
            $table->text('motif_refus')->nullable();
        });
    }
};
