<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Echeance;
use App\Models\Location;

class VerifierEcheancesRetard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'echeances:verifier-retards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie et met à jour le statut des échéances en retard ou impayées';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification des échéances en cours...');
        
        // Récupérer toutes les échéances non payées
        $echeances = Echeance::whereIn('statut', ['a_echeance', 'en_retard', 'partiel', 'impaye'])
            ->where('date_echeance', '<=', now())
            ->get();
        
        $miseAJour = 0;
        $enRetard = 0;
        $impayees = 0;
        
        foreach ($echeances as $echeance) {
            $ancienStatut = $echeance->statut;
            $echeance->mettreAJourStatut();
            
            if ($ancienStatut !== $echeance->statut) {
                $miseAJour++;
            }
            
            if ($echeance->statut == 'en_retard') {
                $enRetard++;
            } elseif ($echeance->statut == 'impaye') {
                $impayees++;
            }
        }
        
        $this->info("✅ Vérification terminée:");
        $this->line("   - {$echeances->count()} échéances vérifiées");
        $this->line("   - {$miseAJour} statuts mis à jour");
        
        if ($enRetard > 0) {
            $this->warn("   ⚠️  {$enRetard} échéances en retard");
        }
        
        if ($impayees > 0) {
            $this->error("   ❌ {$impayees} échéances impayées (>30 jours)");
        }
        
        if ($enRetard == 0 && $impayees == 0) {
            $this->info("   ✨ Aucune échéance en retard!");
        }
        
        return Command::SUCCESS;
    }
}
