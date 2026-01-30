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
            // Modifier l'enum statut pour ajouter les nouveaux statuts
            $table->dropColumn('statut');
        });

        Schema::table('demande_interets', function (Blueprint $table) {
            $table->enum('statut', [
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
            ])->default('nouvelle')->after('message');
            
            // Nouveaux champs
            $table->text('compte_rendu_visite')->nullable()->after('date_visite');
            $table->boolean('client_interesse_apres_visite')->nullable()->after('compte_rendu_visite');
            $table->text('documents_urls')->nullable()->after('pieces_fournies'); // JSON des URLs de documents
            $table->text('raison_refus_dossier')->nullable()->after('documents_urls');
            $table->string('contrat_url')->nullable()->after('raison_refus_dossier');
            $table->dateTime('date_signature_contrat')->nullable()->after('contrat_url');
            $table->decimal('montant_caution', 15, 2)->nullable()->after('date_signature_contrat');
            $table->decimal('montant_loyer_premier', 15, 2)->nullable()->after('montant_caution');
            $table->decimal('montant_frais_agence', 15, 2)->nullable()->after('montant_loyer_premier');
            $table->decimal('montant_total_paiement', 15, 2)->nullable()->after('montant_frais_agence');
            $table->enum('statut_paiement', ['en_attente', 'partiel', 'complet'])->nullable()->after('montant_total_paiement');
            $table->text('details_paiement')->nullable()->after('statut_paiement'); // JSON des paiements effectués
            $table->decimal('commission_agence', 15, 2)->nullable()->after('details_paiement');
            $table->dateTime('date_finalisation')->nullable()->after('commission_agence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_interets', function (Blueprint $table) {
            $table->dropColumn([
                'compte_rendu_visite',
                'client_interesse_apres_visite',
                'documents_urls',
                'raison_refus_dossier',
                'contrat_url',
                'date_signature_contrat',
                'montant_caution',
                'montant_loyer_premier',
                'montant_frais_agence',
                'montant_total_paiement',
                'statut_paiement',
                'details_paiement',
                'commission_agence',
                'date_finalisation'
            ]);
        });
    }
};
