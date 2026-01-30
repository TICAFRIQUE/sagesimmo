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
        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('commission_agence', 15, 2)->nullable()->after('prix_vente');
            $table->enum('type_commission', ['fixe', 'pourcentage'])->default('pourcentage')->after('commission_agence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['commission_agence', 'type_commission']);
        });
    }
};
