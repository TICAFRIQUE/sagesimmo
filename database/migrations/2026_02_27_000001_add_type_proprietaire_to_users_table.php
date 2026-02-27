<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // type_proprietaire: 'externe' ou 'agence' (par défaut 'externe')
            if (!Schema::hasColumn('users', 'type_proprietaire')) {
                // Utiliser enum si supporté par la base, sinon string
                try {
                    $table->enum('type_proprietaire', ['externe', 'agence'])->default('agence')->after('role');
                } catch (\Throwable $e) {
                    $table->string('type_proprietaire', 50)->default('externe')->after('role');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'type_proprietaire')) {
                $table->dropColumn('type_proprietaire');
            }
        });
    }
};
