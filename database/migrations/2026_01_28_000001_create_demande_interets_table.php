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
            $table->enum('statut', [
                'nouvelle',
                'contrat_envoye',
                'visite_planifiee',
                'visite_effectuee',
                'paiement_en_attente',
                'paiement_valide',
                'cloture'
            ])->default('nouvelle');
            $table->dateTime('date_visite')->nullable();
            $table->dateTime('date_finalisation')->nullable();
            $table->text('motif_cloture')->nullable();
            $table->text('note_admin')->nullable();
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
