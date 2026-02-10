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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->text('message_client')->nullable();
            $table->date('date_vente')->nullable();
            
            // Configuration de la vente
            $table->unsignedBigInteger('prix_vente')->nullable();
            $table->unsignedBigInteger('commission_agence')->nullable();
            $table->enum('type_commission', ['pourcentage', 'montant'])->default('montant');
            
            // Workflow
            $table->dateTime('date_visite')->nullable();
            $table->text('compte_rendu_visite')->nullable();
             $table->boolean('client_interesse_visite')->nullable(); // Nouveau champ pour indiquer si le prospect est intéressé ou non après la visite
            $table->boolean('client_interesse_retour')->nullable(); // Nouveau champ pour indiquer si le prospect est intéressé ou non après le retour de la fiche
            $table->date('date_signature')->nullable();
            $table->dateTime('date_finalisation')->nullable();
            $table->enum('statut', ['demande_client', 'brouillon', 'fiche_envoyee', 'retour_prospect', 'visite_planifiee', 'offre_acceptee', 'terminee', 'annulee'])->default('demande_client');
            $table->text('note_admin')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
