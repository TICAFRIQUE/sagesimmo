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
        // Mise à jour table ventes
        Schema::table('ventes', function (Blueprint $table) {
            $table->text('message_client')->nullable()->after('client_id');
            $table->dateTime('date_visite')->nullable()->after('date_signature');
            $table->text('compte_rendu_visite')->nullable()->after('date_visite');
            $table->text('note_admin')->nullable()->after('notes');
            $table->decimal('montant_caution', 15, 2)->nullable()->after('prix_vente');
            $table->decimal('montant_frais_agence', 15, 2)->nullable()->after('commission_agence');
            $table->dateTime('date_finalisation')->nullable()->after('date_signature');
        });

        // Nouveau ENUM pour ventes avec workflow
        DB::statement("ALTER TABLE ventes MODIFY COLUMN statut ENUM(
            'demande_client',
            'fiche_envoyee',
            'visite_planifiee',
            'en_attente_paiement',
            'paiement_valide',
            'annule'
        ) DEFAULT 'demande_client'");

        // Mise à jour table locations
        Schema::table('locations', function (Blueprint $table) {
            $table->text('message_client')->nullable()->after('locataire_id');
            $table->dateTime('date_visite')->nullable()->after('date_fin');
            $table->text('compte_rendu_visite')->nullable()->after('date_visite');
            $table->text('note_admin')->nullable()->after('conditions');
            $table->decimal('montant_frais_agence', 15, 2)->nullable()->after('caution');
            $table->dateTime('date_finalisation')->nullable()->after('date_fin');
        });

        // Nouveau ENUM pour locations avec workflow
        DB::statement("ALTER TABLE locations MODIFY COLUMN statut ENUM(
            'demande_client',
            'fiche_envoyee',
            'visite_planifiee',
            'en_attente_paiement',
            'actif',
            'termine',
            'resilie'
        ) DEFAULT 'demande_client'");

        // Convertir les anciens statuts
        DB::statement("UPDATE ventes SET statut = 'paiement_valide' WHERE statut = 'completé'");
        DB::statement("UPDATE ventes SET statut = 'annule' WHERE statut = 'annulé'");
        DB::statement("UPDATE ventes SET statut = 'en_attente_paiement' WHERE statut = 'en_cours'");
        
        DB::statement("UPDATE locations SET statut = 'actif' WHERE statut = 'actif'");
        DB::statement("UPDATE locations SET statut = 'resilie' WHERE statut = 'résilié'");
        DB::statement("UPDATE locations SET statut = 'termine' WHERE statut = 'terminé'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer ancien ENUM ventes
        DB::statement("ALTER TABLE ventes MODIFY COLUMN statut ENUM('en_cours', 'completé', 'annulé') DEFAULT 'en_cours'");
        
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn([
                'message_client',
                'date_visite',
                'compte_rendu_visite',
                'note_admin',
                'montant_caution',
                'montant_frais_agence',
                'date_finalisation'
            ]);
        });

        // Restaurer ancien ENUM locations
        DB::statement("ALTER TABLE locations MODIFY COLUMN statut ENUM('actif', 'terminé', 'résilié') DEFAULT 'actif'");
        
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'message_client',
                'date_visite',
                'compte_rendu_visite',
                'note_admin',
                'montant_frais_agence',
                'date_finalisation'
            ]);
        });
    }
};
