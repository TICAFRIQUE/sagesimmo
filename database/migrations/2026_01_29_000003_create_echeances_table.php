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
        Schema::create('echeances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->date('date_echeance');
            $table->decimal('montant_du', 15, 2);
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->enum('statut', ['a_echeance', 'en_retard', 'partiel', 'paye', 'impaye', 'cloture'])->default('a_echeance');
            $table->decimal('commission_agence', 15, 2)->default(0)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};
