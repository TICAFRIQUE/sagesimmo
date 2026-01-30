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
        Schema::create('demande_interets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->text('message');
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee', 'visite_proposee', 'pieces_demandees'])->default('en_attente');
            $table->dateTime('date_visite')->nullable();
            $table->text('pieces_demandees')->nullable(); // Liste des pièces demandées
            $table->text('pieces_fournies')->nullable(); // Chemin des pièces fournies (JSON)
            $table->text('motif_refus')->nullable();
            $table->text('note_admin')->nullable(); // Notes internes de l'admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_interets');
    }
};
