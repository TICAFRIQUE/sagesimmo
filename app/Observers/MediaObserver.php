<?php

namespace App\Observers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class MediaObserver
{
    /**
     * Handle the Media "saved" event.
     * Ajoute automatiquement un watermark sur les images des biens
     */
    public function saved(Media $media): void
    {
        // Vérifier si c'est une image d'une annonce
        if ($media->model_type !== 'App\Models\Annonce') {
            return;
        }

        // Vérifier si c'est une collection d'images (pas documents)
        if (!in_array($media->collection_name, ['images', 'image_principale'])) {
            return;
        }

        // Vérifier que c'est bien une image
        if (!str_starts_with($media->mime_type, 'image/')) {
            return;
        }

        // Attendre que le fichier soit complètement écrit
        sleep(1);
        
        $this->addWatermark($media);
    }

    /**
     * Ajoute le watermark sur l'image
     */
    protected function addWatermark(Media $media): void
    {
        $logoPath = public_path('images/logo/logo.png');
        
        // Normaliser le chemin pour Windows
        $mediaPath = str_replace('/', DIRECTORY_SEPARATOR, $media->getPath());

        if (!file_exists($logoPath)) {
            Log::warning('Logo introuvable pour le watermark: ' . $logoPath);
            return;
        }

        if (!file_exists($mediaPath)) {
            Log::warning('Media introuvable: ' . $mediaPath);
            return;
        }

        try {
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
            
            Log::info('Watermark ajouté avec succès sur: ' . $media->file_name);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du watermark sur ' . $media->file_name . ': ' . $e->getMessage());
        }
    }
}
