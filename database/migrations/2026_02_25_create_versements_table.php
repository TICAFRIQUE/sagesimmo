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
        Schema::create('versements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprietaire_id')->constrained('users')->onDelete('cascade');
            $table->double('montant');
            $table->date('date_versement');
            $table->date('date_debut')->nullable(); // Début de la période
            $table->date('date_fin')->nullable(); // Fin de la période
            $table->enum('mode_versement', ['virement', 'chèque', 'espèces', 'mobile_money', 'autre'])->default('virement');
            $table->string('reference')->nullable(); // Numéro de virement, chèque, etc.
            $table->enum('statut', ['en_attente', 'effectue', 'annule' , 'partiel' , ])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index pour les requêtes fréquentes
            $table->index(['proprietaire_id', 'date_versement']);
            $table->index(['proprietaire_id', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versements');
    }
};
