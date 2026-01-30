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
            $table->foreignId('demande_interet_id')->nullable()->after('id')->constrained('demande_interets')->onDelete('cascade');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('demande_interet_id')->nullable()->after('id')->constrained('demande_interets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropForeign(['demande_interet_id']);
            $table->dropColumn('demande_interet_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['demande_interet_id']);
            $table->dropColumn('demande_interet_id');
        });
    }
};
