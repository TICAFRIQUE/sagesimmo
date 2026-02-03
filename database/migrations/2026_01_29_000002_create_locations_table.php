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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_interet_id')->nullable()->constrained('demande_interets')->onDelete('set null');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->foreignId('locataire_id')->constrained('users')->onDelete('cascade');
            $table->text('message_client')->nullable();
            
            // Configuration des paiements
            $table->decimal('loyer_mensuel', 15, 2)->nullable();
            $table->integer('avance_sur_loyer')->default(0); // Nombre de mois d'avance
            $table->decimal('montant_avance', 15, 2)->default(0);
            $table->boolean('premier_paiement_valide')->default(false);
            $table->integer('nombre_cautions')->default(2);
            $table->decimal('caution', 15, 2)->nullable();
            $table->decimal('montant_frais_agence', 15, 2)->nullable();
            $table->decimal('commission_agence', 10, 2)->nullable();
            $table->enum('type_commission', ['pourcentage', 'montant'])->default('montant');
            
            // Dates et workflow
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->dateTime('date_visite')->nullable();
            $table->text('compte_rendu_visite')->nullable();
            $table->dateTime('date_finalisation')->nullable();
            $table->enum('statut', ['demande_client', 'brouillon', 'fiche_envoyee', 'visite_planifiee', 'en_attente_paiement', 'actif', 'resilie'])->default('demande_client');
            $table->integer('jour_paiement')->nullable();
            $table->text('conditions')->nullable();
            $table->text('note_admin')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
