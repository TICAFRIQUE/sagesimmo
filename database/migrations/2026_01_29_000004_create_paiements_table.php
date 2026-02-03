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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable'); // Pour ventes ou locations
            $table->foreignId('echeance_id')->nullable()->constrained('echeances')->onDelete('set null');
            $table->enum('type_paiement', ['loyer', 'caution', 'avance', 'frais_agence', 'prix_achat', 'arrhes'])->default('loyer');
            $table->enum('statut', ['en_attente', 'paye', 'partiel', 'annule'])->default('en_attente');
            $table->decimal('montant', 15, 2);
            $table->decimal('commission_agence', 10, 2)->nullable();
            $table->enum('type_commission', ['pourcentage', 'montant'])->nullable();
            $table->date('date_paiement');
            $table->enum('methode_paiement', ['espèces', 'virement', 'chèque', 'carte_bancaire', 'mobile_money', 'autre'])->default('virement');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
