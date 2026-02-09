<?php

namespace App\Console\Commands;

use App\Models\Annonce;
use Illuminate\Console\Command;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AddWatermarkToExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:add-watermark {--force : Force l\'ajout même si le watermark existe déjà}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ajoute le watermark du logo sur toutes les images existantes des annonces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de l\'ajout du watermark sur les images existantes...');
        
        $logoPath = public_path('images/logo/logo.png');
        
        if (!file_exists($logoPath)) {
            $this->error('Le logo n\'existe pas à l\'emplacement: ' . $logoPath);
            return 1;
        }

        // Récupérer toutes les images des annonces
        $medias = Media::where('model_type', 'App\Models\Annonce')
            ->whereIn('collection_name', ['images', 'image_principale'])
            ->where('mime_type', 'like', 'image/%')
            ->get();

        $this->info('Total d\'images trouvées: ' . $medias->count());
        
        $progressBar = $this->output->createProgressBar($medias->count());
        $progressBar->start();

        $success = 0;
        $errors = 0;

        foreach ($medias as $media) {
            try {
                $mediaPath = $media->getPath();
                
                if (!file_exists($mediaPath)) {
                    $this->newLine();
                    $this->warn('Fichier introuvable: ' . $mediaPath);
                    $errors++;
                    $progressBar->advance();
                    continue;
                }

                // Charger l'image
                $image = Image::read($mediaPath);
                
                // Charger le logo
                $logo = Image::read($logoPath);
                
                // Calculer la taille du logo (15% de la largeur de l'image)
                $logoWidth = (int)($image->width() * 0.15);
                
                // Redimensionner le logo en gardant le ratio
                $logo->scale(width: $logoWidth);
                
                // Ajouter le logo dans le coin inférieur droit avec marge de 20px et opacité de 80%
                $image->place($logo, 'bottom-right', 20, 20, 80);
                
                // Sauvegarder l'image avec le watermark
                $image->save($mediaPath);
                
                $success++;
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error('Erreur sur ' . $media->file_name . ': ' . $e->getMessage());
                Log::error('Erreur watermark: ' . $e->getMessage());
                $errors++;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info('Traitement terminé!');
        $this->info('Images traitées avec succès: ' . $success);
        
        if ($errors > 0) {
            $this->warn('Erreurs rencontrées: ' . $errors);
        }

        return 0;
    }
}
