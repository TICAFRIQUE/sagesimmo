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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icone')->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // // Table pivot pour la relation many-to-many
        // Schema::create('annonce_equipement', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
        //     $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonce_equipement');
        Schema::dropIfExists('equipements');
    }
};
