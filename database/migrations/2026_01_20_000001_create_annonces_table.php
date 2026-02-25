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
            $table->string('titre')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->enum('type_transaction', ['vente', 'location'])->default('vente');
            $table->foreignId('type_bien_id')->constrained('type_biens')->onDelete('cascade');
            $table->unsignedBigInteger('prix');
            $table->unsignedBigInteger('commission')->nullable()->comment('Commission en pourcentage ou montant fixe');
            $table->enum('type_commission', ['pourcentage', 'fixe'])->nullable()->default('pourcentage');
            $table->unsignedInteger('surface')->nullable();
            $table->integer('nombre_chambres')->nullable();
            $table->integer('nombre_salles_bain')->nullable();
            $table->integer('nombre_pieces')->nullable();
            $table->integer('etage')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
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
            $table->foreignId('proprietaire_id')->nullable()->constrained('users')->onDelete('cascade')->comment('Le propriétaire du bien');
            // Ajouter un champ pour indiquer si le bien appartient à l'agence
            $table->boolean('est_bien_agence')->default(false)
                ->comment('Indique si le bien appartient à l\'agence ou à un propriétaire externe');

            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('cascade')->comment('L\'utilisateur qui a créé l\'annonce');
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
