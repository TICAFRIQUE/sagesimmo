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
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('slug')->nullable();
            $table->text('description');
            $table->enum('type_transaction', ['vente', 'location'])->default('vente');
            $table->foreignId('type_bien_id')->constrained('type_biens')->onDelete('cascade');
            $table->decimal('prix', 15, 2);
            $table->decimal('commission', 10, 2)->nullable()->comment('Commission en pourcentage ou montant fixe');
            $table->enum('type_commission', ['pourcentage', 'fixe'])->nullable()->default('pourcentage');
            $table->decimal('surface', 10, 2)->nullable();
            $table->integer('nombre_chambres')->nullable();
            $table->integer('nombre_salles_bain')->nullable();
            $table->integer('nombre_pieces')->nullable();
            $table->integer('etage')->nullable();
            $table->string('adresse');
            $table->string('ville');
            $table->string('commune')->nullable();
            $table->string('quartier')->nullable();
            $table->string('code_postal')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('statut', ['disponible', 'loue', 'vendu', 'en_attente'])->default('disponible');
            $table->boolean('en_vedette')->default(false);
            $table->date('date_disponibilite')->nullable();
            $table->integer('annee_construction')->nullable();
            $table->text('caracteristiques_supplementaires')->nullable();
            $table->string('reference')->unique();
            $table->foreignId('proprietaire_id')->nullable()->constrained('users')->onDelete('set null')->comment('Le propriétaire du bien');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null')->comment('L\'utilisateur qui a créé l\'annonce');
            $table->integer('nombre_vues')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation many-to-many
        Schema::create('annonce_equipement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
