<?php

namespace App\Console\Commands;

use App\Models\Annonce;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateAnnonceSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annonces:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les slugs manquants pour les annonces existantes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Génération des slugs pour les annonces...');

        $annonces = Annonce::whereNull('slug')->orWhere('slug', '')->get();

        if ($annonces->isEmpty()) {
            $this->info('Toutes les annonces ont déjà un slug.');
            return 0;
        }

        $bar = $this->output->createProgressBar($annonces->count());
        $bar->start();

        foreach ($annonces as $annonce) {
            // Le trait Sluggable va automatiquement générer le slug
            $annonce->slug = null; // Reset pour forcer la régénération
            $annonce->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ {$annonces->count()} slug(s) généré(s) avec succès.");

        return 0;
    }
}
