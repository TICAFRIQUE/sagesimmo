<?php

namespace App\Http\Controllers\backend;

use App\Models\Parametre;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ParametreController extends Controller
{
    public function index()
    {
        $data_parametre = Parametre::with('media')->first();

        //get status mode maintenance
        // $data_maintenance = Maintenance::latest()->select('type')->first();

        // recuperer la liste des sauvegardes du projet
        $appName = config('app.name');
        $backup = Storage::disk('local')->files('' . $appName . '/');

        
        // dd($data_parametre->toArray());
        return view('backend.pages.parametre.index', compact('data_parametre',  'backup'));
    }

    // Télécharger un fichier de sauvegarde
    public function downloadBackup($file)
    {
        // $path = "Restaurant/" . $file;

        // if (Storage::disk('local')->exists($path)) {
        //     return Storage::disk('local')->download($path);
        // }
        $appName = config('app.name');
        $path = storage_path("app/private/" . $appName . "/" . $file);

        if (file_exists($path)) {
            return response()->download($path);
        }

        Alert::error('Fichier non trouvé.', 'Error Message');

        return back();

        // return redirect()->back()->with('error', 'Fichier non trouvé.');
    }


    public function store(Request $request)
    {
        try {
            // Vérifier si un paramètre existe déjà
            $data_parametre = Parametre::first();

            if ($data_parametre) {
                // Mettre à jour les données existantes
                $data_parametre->update([
                    'lien_facebook' => $request['lien_facebook'],
                    'lien_instagram' => $request['lien_instagram'],
                    'lien_twitter' => $request['lien_twitter'],
                    'lien_linkedin' => $request['lien_linkedin'],
                    'lien_tiktok' => $request['lien_tiktok'],

                    //infos application
                    'nom_projet' => $request['nom_projet'],
                    'description_projet' => $request['description_projet'],
                    'contact_principal' => $request['contact_principal'],
                    'contact_secondaire' => $request['contact_secondaire'],
                    'contact_whatsapp' => $request['contact_whatsapp'],

                    'email_principal' => $request['email_principal'],
                    'email_secondaire' => $request['email_secondaire'],

                    'localisation' => $request['localisation'],
                    'google_maps' => $request['google_maps'],
                    'siege_social' => $request['siege_social'],
                ]);

                // Recharger l'instance pour avoir les dernières données
                $data_parametre->refresh();
            } else {
                // Créer un nouveau paramètre
                $data_parametre = Parametre::create([
                    'lien_facebook' => $request['lien_facebook'],
                    'lien_instagram' => $request['lien_instagram'],
                    'lien_twitter' => $request['lien_twitter'],
                    'lien_linkedin' => $request['lien_linkedin'],
                    'lien_tiktok' => $request['lien_tiktok'],

                    //infos application
                    'nom_projet' => $request['nom_projet'],
                    'description_projet' => $request['description_projet'],
                    'contact_principal' => $request['contact_principal'],
                    'contact_secondaire' => $request['contact_secondaire'],
                    'contact_whatsapp' => $request['contact_whatsapp'],

                    'email_principal' => $request['email_principal'],
                    'email_secondaire' => $request['email_secondaire'],

                    'localisation' => $request['localisation'],
                    'google_maps' => $request['google_maps'],
                    'siege_social' => $request['siege_social'],
                ]);
            }

            // Gestion des images - logique simplifiée et corrigée
            $mediaTypes = ['cover', 'logo_header', 'logo_footer'];
            
            foreach ($mediaTypes as $mediaType) {
                if ($request->hasFile($mediaType)) {
                    // Supprimer l'ancien média s'il existe
                    if ($data_parametre->hasMedia($mediaType)) {
                        $data_parametre->clearMediaCollection($mediaType);
                    }
                    
                    // Ajouter le nouveau média
                    $data_parametre->addMediaFromRequest($mediaType)
                        ->toMediaCollection($mediaType);
                }
            }

            Alert::success('Paramètres mis à jour avec succès', 'Succès');
            return back();
            
        } catch (\Throwable $th) {
            Alert::error('Erreur: ' . $th->getMessage(), 'Erreur');
            return back()->withInput();
        }
    }


    /**
     * Supprime les fichiers de cache de l'application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function optimizeClear()
    {
        Artisan::call('optimize:clear');
        return response()->json(['message' => 'cache clear', 'status' => 200], 200);
    }


    /**
     * Desactive le mode maintenance de l'application.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function maintenanceUp()
    {

        Artisan::call('up');
        Parametre::first()->update([
            'mode_maintenance' => 'up',
        ]);
        return response()->json(['message' => 'mode maintenance desactivé', 'status' => 200], 200);
    }


    /**
     * Active le mode maintenance de l'application.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function maintenanceDown()
    {

        Artisan::call('down', [
            '--secret' => 'admin@',
            '--render' => 'backend.pages.maintenance-mode-view',
        ]);
        Parametre::first()->update([
            'mode_maintenance' => 'down',
        ]);
        return response()->json(['message' => 'mode maintenance activé', 'status' => 200], 200);
    }
}
