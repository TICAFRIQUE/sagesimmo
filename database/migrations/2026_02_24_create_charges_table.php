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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('annonce_id');
            $table->enum('type_charge', ['maintenance', 'reparation', 'taxe', 'autre'])->default('autre');
            $table->double('montant');
            $table->date('date_charge');
            $table->text('description')->nullable();
            $table->string('reference')->nullable(); // Numéro facture, etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('annonce_id')->references('id')->on('annonces')->onDelete('cascade');
            
            // Index
            $table->index('annonce_id');
            $table->index('date_charge');
            $table->index('type_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
