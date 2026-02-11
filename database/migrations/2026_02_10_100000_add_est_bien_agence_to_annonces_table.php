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
        Schema::table('annonces', function (Blueprint $table) {
            // Ajouter un champ pour indiquer si le bien appartient à l'agence
            $table->boolean('est_bien_agence')->default(false)->after('proprietaire_id')
                ->comment('Indique si le bien appartient à l\'agence ou à un propriétaire externe');
            
            // Modifier proprietaire_id pour qu'il soit nullable
            // Car si le bien appartient à l'agence, on n'a pas forcément besoin d'un propriétaire
            $table->foreignId('proprietaire_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('est_bien_agence');
            // Note: La modification de nullable ne peut pas être facilement inversée
            // sans perdre de données potentielles
        });
    }
};
