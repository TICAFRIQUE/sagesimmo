<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Charge;
use App\Models\Location;
use App\Models\Paiement;
use App\Models\User;
use App\Models\Vente;
use App\Services\RapportAcheteurService;
use App\Services\RapportAgenceService;
use App\Services\RapportLocataireService;
use App\Services\RapportProprietaireService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RapportController extends Controller
{
    /**
     * Rapport des commissions
     */
    public function commissions(Request $request)
    {
        // Récupérer les filtres
        $dateDebut = $request->input('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->endOfMonth()->format('Y-m-d'));
        $typeTransaction = $request->input('type_transaction', 'tous'); // tous, location, vente
        $locataire = $request->input('locataire');

        // Commissions des locations (paiements de loyer uniquement)
        $commissionsLocations = collect();
        if (in_array($typeTransaction, ['tous', 'location'])) {
            $commissionsLocations = Paiement::with(['payable.locataire', 'payable.annonce'])
                ->where('payable_type', Location::class)
                ->where('type_paiement', 'loyer') // Seulement les loyers, pas la caution ni l'avance
                ->where('statut', 'paye')
                ->whereBetween('date_paiement', [$dateDebut, $dateFin])
                ->when($locataire, function ($query, $locataire) {
                    $query->whereHas('payable', function ($q) use ($locataire) {
                        $q->where('locataire_id', $locataire);
                    });
                })
                ->get()
                ->filter(function ($paiement) {
                    // Filtrer uniquement les paiements dont la location a une commission
                    return $paiement->payable && $paiement->payable->commission_agence;
                })
                ->map(function ($paiement) {
                    $montant = floatval($paiement->montant);

                    // Récupérer la commission depuis la location, pas depuis le paiement
                    $location = $paiement->payable;
                    $commissionAgence = floatval($location->commission_agence ?? 0);
                    $typeCommission = $location->type_commission ?? 'montant';

                    // Calculer la commission sur ce paiement de loyer
                    $commission = $typeCommission == 'pourcentage'
                        ? ($montant * $commissionAgence / 100)
                        : $commissionAgence;

                    return [
                        'id' => $paiement->id,
                        'location_id' => $location->id,
                        'date' => $paiement->date_paiement,
                        'type' => 'Location',
                        'reference' => $location->annonce->reference ?? 'N/A',
                        'bien' => $location->annonce->titre ?? 'N/A',
                        'client' => $location->locataire->name ?? 'N/A',
                        'montant_transaction' => $montant,
                        'commission_config' => $commissionAgence . ($typeCommission == 'pourcentage' ? '%' : ' FCFA'),
                        'commission_montant' => $commission,
                        'methode_paiement' => $paiement->methode_paiement,
                        'reference_paiement' => $paiement->reference,
                    ];
                });
        }

        // Commissions des ventes (récupérer depuis le modèle Vente)
        $commissionsVentes = collect();
        if (in_array($typeTransaction, ['tous', 'vente'])) {
            // Récupérer les ventes avec commission et qui ont des paiements dans la période
            $ventes = Vente::with(['client', 'annonce', 'paiements'])
                ->whereNotNull('commission_agence')
                ->where('commission_agence', '>', 0)
                ->whereHas('paiements', function ($query) use ($dateDebut, $dateFin) {
                    $query->where('statut', 'paye')
                        ->whereBetween('date_paiement', [$dateDebut, $dateFin]);
                })
                ->get();

            $commissionsVentes = $ventes->map(function ($vente) {
                // Utiliser la date du dernier paiement comme date de référence
                $dernierPaiement = $vente->paiements()
                    ->where('statut', 'paye')
                    ->orderBy('date_paiement', 'desc')
                    ->first();

                if (!$dernierPaiement) {
                    return null;
                }

                // Calculer la commission selon le type
                $commissionAgence = floatval($vente->commission_agence);
                if ($vente->type_commission === 'pourcentage') {
                    $commission = ($vente->prix_vente * $commissionAgence) / 100;
                } else {
                    $commission = $commissionAgence;
                }

                return [
                    'id' => $vente->id,
                    'vente_id' => $vente->id,
                    'date' => $dernierPaiement->date_paiement,
                    'type' => 'Vente',
                    'reference' => $vente->annonce->reference ?? 'N/A',
                    'bien' => $vente->annonce->titre ?? 'N/A',
                    'client' => $vente->client->name ?? 'N/A',
                    'montant_transaction' => floatval($vente->prix_vente),
                    'commission_config' => $commissionAgence . ($vente->type_commission == 'pourcentage' ? '%' : ' FCFA'),
                    'commission_montant' => $commission,
                    'methode_paiement' => $dernierPaiement->methode_paiement,
                    'reference_paiement' => $dernierPaiement->reference,
                ];
            })->filter(); // Filtrer les valeurs null
        }

        // Statistiques séparées pour chaque type
        $totalCommissionsLocations = $commissionsLocations->sum('commission_montant');
        $totalTransactionsLocations = $commissionsLocations->sum('montant_transaction');
        $nombreLocations = $commissionsLocations->count();

        $totalCommissionsVentes = $commissionsVentes->sum('commission_montant');
        $totalTransactionsVentes = $commissionsVentes->sum('montant_transaction');
        $nombreVentes = $commissionsVentes->count();

        // Statistiques globales
        $totalCommissions = $totalCommissionsLocations + $totalCommissionsVentes;
        $totalTransactions = $totalTransactionsLocations + $totalTransactionsVentes;
        $nombreTransactions = $nombreLocations + $nombreVentes;
        $commissionMoyenne = $nombreTransactions > 0 ? $totalCommissions / $nombreTransactions : 0;

        // Trier chaque collection par date
        $commissionsLocations = $commissionsLocations->sortByDesc('date')->values();
        $commissionsVentes = $commissionsVentes->sortByDesc('date')->values();

        // Liste des locataires pour le filtre
        $locataires = \App\Models\User::whereHas('locations')->get();

        return view('backend.pages.rapports.commissions', compact(
            'commissionsLocations',
            'commissionsVentes',
            'totalCommissionsLocations',
            'totalTransactionsLocations',
            'nombreLocations',
            'totalCommissionsVentes',
            'totalTransactionsVentes',
            'nombreVentes',
            'totalCommissions',
            'totalTransactions',
            'nombreTransactions',
            'commissionMoyenne',
            'dateDebut',
            'dateFin',
            'typeTransaction',
            'locataires',
            'locataire'
        ));
    }

    /**
     * Statistiques générales
     */
    public function statistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfYear()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->endOfYear()->format('Y-m-d'));

        // Statistiques Locations
        $locationsActives = Location::where('statut', 'actif')->count();
        $totalLoyers = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');

        $commissionsLoyers = Paiement::where('payable_type', Location::class)
            ->where('type_paiement', 'loyer')
            ->where('statut', 'paye')
            ->whereNotNull('commission_agence')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get()
            ->sum(function ($paiement) {
                $montant = floatval($paiement->montant);
                $commission = floatval($paiement->commission_agence);
                return $paiement->type_commission == 'pourcentage'
                    ? ($montant * $commission / 100)
                    : $commission;
            });

        // Statistiques Ventes
        $ventesCompletes = Vente::where('statut', 'terminee')->count();
        $totalVentes = Paiement::where('payable_type', Vente::class)
            ->where('type_paiement', 'prix_achat')
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');

        $commissionsVentes = Paiement::where('payable_type', Vente::class)
            ->where('type_paiement', 'prix_achat')
            ->where('statut', 'paye')
            ->whereNotNull('commission_agence')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get()
            ->sum(function ($paiement) {
                $montant = floatval($paiement->montant);
                $commission = floatval($paiement->commission_agence);
                return $paiement->type_commission == 'pourcentage'
                    ? ($montant * $commission / 100)
                    : $commission;
            });

        // Évolution mensuelle
        $evolutionMensuelle = Paiement::select(
            DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as mois'),
            DB::raw('SUM(montant) as total'),
            DB::raw('COUNT(*) as nombre')
        )
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Top 5 biens les plus rentables
        $topBiens = Paiement::with(['payable.annonce'])
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->get()
            ->groupBy(function ($paiement) {
                return ($paiement->payable_type ?? '') . '-' . ($paiement->payable_id ?? 'autre');
            })
            ->map(function ($group) {
                $commission = $group->sum(function ($paiement) {
                    if (!$paiement->commission_agence) return 0;
                    $montant = floatval($paiement->montant);
                    $commissionAgence = floatval($paiement->commission_agence);
                    return $paiement->type_commission == 'pourcentage'
                        ? ($montant * $commissionAgence / 100)
                        : $commissionAgence;
                });

                $first = $group->first();
                $bien = $first->payable?->annonce ?? null;

                return [
                    'bien' => $bien?->titre ?? 'N/A',
                    'reference' => $bien?->reference ?? 'N/A',
                    'total' => $group->sum(function ($paiement) {
                        return floatval($paiement->montant);
                    }),
                    'commission' => $commission,
                    'nombre_transactions' => $group->count(),
                ];
            })
            ->sortByDesc('commission')
            ->take(5);

        return view('backend.pages.rapports.statistiques', compact(
            'locationsActives',
            'totalLoyers',
            'commissionsLoyers',
            'ventesCompletes',
            'totalVentes',
            'commissionsVentes',
            'evolutionMensuelle',
            'topBiens',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Rapport financier propriétaire - Affiche les revenus d'un propriétaire
     */
    public function rapportProprietaire(Request $request)
    {
        // Seul les administrateurs peuvent voir le rapport propriétaire
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'proprietaire_id' => 'nullable|exists:users,id',
        ]);

        // Déterminer le propriétaire
        $proprietaireId = $request->input('proprietaire_id');

        // Si pas de propriétaire spécifié, afficher liste de sélection
        if (!$proprietaireId) {
            // Récupérer les filtres
            $proprietaireFiltre = $request->input('proprietaire_filtre');
            $statutFiltre = $request->input('statut_filtre'); // Nouveau filtre de statut
            $dateDebut = $request->input('date_debut')
                ? Carbon::parse($request->input('date_debut'))->startOfDay()
                : now()->startOfMonth();
            $dateFin = $request->input('date_fin')
                ? Carbon::parse($request->input('date_fin'))->endOfDay()
                : now()->endOfMonth();

            // Récupérer tous les propriétaires pour le select
            $allProprietaires = User::where('role', 'proprietaire')->get();

            // Filtrer les propriétaires à afficher
            $proprietaires = $allProprietaires;
            if ($proprietaireFiltre) {
                $proprietaires = $allProprietaires->where('id', $proprietaireFiltre);
            }

            // Générer un aperçu du rapport pour chaque propriétaire selon les dates
            // On indexe par l'ID du propriétaire pour éviter les problèmes d'index
            $service = new RapportProprietaireService();
            $aperçus = $proprietaires->keyBy('id')->map(function ($proprietaire) use ($service, $dateDebut, $dateFin) {
                return $service->genererRapport($proprietaire, $dateDebut, $dateFin);
            });

            // Filtrer par statut si demandé
            if ($statutFiltre) {
                $proprietaires = $proprietaires->filter(function ($proprietaire) use ($aperçus, $statutFiltre) {
                    $rapport = $aperçus[$proprietaire->id] ?? null;
                    if (!$rapport) return false;

                    $badge = $rapport['statut_versement']['badge'] ?? 'secondary';

                    // Mapper les badges aux statuts
                    $badgeToStatut = [
                        'warning' => 'en_attente',
                        'info' => 'partiel',
                        'success' => 'effectue',
                        'secondary' => 'aucun'
                    ];

                    return ($badgeToStatut[$badge] ?? 'aucun') === $statutFiltre;
                });

                // Filtrer les aperçus pour ne garder que ceux des propriétaires restants
                $aperçus = $aperçus->only($proprietaires->pluck('id'));
            }

            // Calculer les KPI globaux
            $kpiGlobal = [
                'versements_disponibles' => $aperçus->sum('reste_a_verser') ?? 0, // À verser
                'versements_partiels' => $aperçus->sum('total_versements_partiels') ?? 0, // Montant partiel
                'versements_effectues' => $aperçus->sum('montant_total_verse') ?? 0, // Total versé
                'total_commission' => $aperçus->sum('total_commission_agence') ?? 0, // Commission totale perçue
                'total_frais_agence' => $aperçus->sum('total_frais_agence') ?? 0, // Frais agence perçus
            ];

            return view('backend.pages.rapports.proprietaire-select', compact(
                'proprietaires',
                'allProprietaires',
                'aperçus',
                'dateDebut',
                'dateFin',
                'kpiGlobal'
            ));
        }

        $proprietaire = User::findOrFail($proprietaireId);

        // Filtres de dates
        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : now()->startOfMonth();

        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : now()->endOfMonth();

        // Générer le rapport
        $service = new RapportProprietaireService();
        $rapport = $service->genererRapport($proprietaire, $dateDebut, $dateFin);

        // Liste des propriétaires pour le filtre
        $proprietaires = User::where('role', 'proprietaire')->get();

        return view('backend.pages.rapports.proprietaire', compact(
            'rapport',
            'proprietaire',
            'dateDebut',
            'dateFin',
            'proprietaires'
        ));
    }

    /**
     * Télécharger le rapport propriétaire en PDF
     */
    public function telechargerRapportProprietaire(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $proprietaireId = $request->input('proprietaire_id');
        if (!$proprietaireId) {
            return redirect()->back()->with('error', 'Propriétaire non spécifié');
        }

        $proprietaire = User::findOrFail($proprietaireId);

        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : now()->startOfMonth();
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : now()->endOfMonth();

        $service = new RapportProprietaireService();
        $rapport = $service->genererRapport($proprietaire, $dateDebut, $dateFin);

        $pdf = Pdf::loadView('backend.pages.rapports.pdf.proprietaire-rapport', compact(
            'rapport',
            'proprietaire',
            'dateDebut',
            'dateFin'
        ))->setPaper('a4', 'portrait');

        $nomFichier = 'rapport_' . str_replace(' ', '_', $proprietaire->username) . '_' . $dateDebut->format('Ymd') . '_' . $dateFin->format('Ymd') . '.pdf';

        return $pdf->download($nomFichier);
    }

    /**
     * Télécharger le rapport global propriétaires en PDF
     */
    public function telechargerRapportGlobal(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : now()->startOfMonth();
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : now()->endOfMonth();

        $proprietaires = User::where('role', 'proprietaire')->get();

        $service = new RapportProprietaireService();
        $aperçus = $proprietaires->keyBy('id')->map(function ($proprietaire) use ($service, $dateDebut, $dateFin) {
            return $service->genererRapport($proprietaire, $dateDebut, $dateFin);
        });

        $kpiGlobal = [
            'versements_disponibles' => $aperçus->sum('reste_a_verser') ?? 0,
            'versements_partiels' => $aperçus->sum('total_versements_partiels') ?? 0,
            'versements_effectues' => $aperçus->sum('montant_total_verse') ?? 0,
            'total_commission' => $aperçus->sum('total_commission_agence') ?? 0,
            'total_frais_agence' => $aperçus->sum('total_frais_agence') ?? 0,
        ];

        $pdf = Pdf::loadView('backend.pages.rapports.pdf.proprietaire-select-rapport', compact(
            'proprietaires',
            'aperçus',
            'dateDebut',
            'dateFin',
            'kpiGlobal'
        ))->setPaper('a4', 'landscape');

        $nomFichier = 'rapport_global_proprietaires_' . $dateDebut->format('Ymd') . '_' . $dateFin->format('Ymd') . '.pdf';

        return $pdf->download($nomFichier);
    }

    /**
     * Rapport financier agence - Affiche les revenus de l'agence
     */
    public function rapportAgence(Request $request)
    {
        // Seul les administrateurs peuvent voir le rapport agence
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        // Filtres de dates
        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : now()->startOfYear();

        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : now()->endOfYear();

        // Générer le rapport
        $service = new RapportAgenceService();
        $rapport = $service->genererRapport($dateDebut, $dateFin);

        return view('backend.pages.rapports.agence', compact(
            'rapport',
            'dateDebut',
            'dateFin'
        ));
    }

  

    // =====================================================
    // RAPPORT LOCATAIRES
    // =====================================================

    /**
     * Rapport locataires - Liste ou détail
     */
    public function rapportLocataire(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'locataire_id' => 'nullable|exists:users,id',
        ]);

        $locataireId = $request->input('locataire_id');
        $service = new RapportLocataireService();

        if (!$locataireId) {
            // Liste de tous les locataires
            $locataireFiltre = $request->input('locataire_filtre');
            $statutFiltre = $request->input('statut_filtre');
            $dateDebut = $request->input('date_debut')
                ? Carbon::parse($request->input('date_debut'))->startOfDay()
                : null;
            $dateFin = $request->input('date_fin')
                ? Carbon::parse($request->input('date_fin'))->endOfDay()
                : null;

            $allLocataires = User::where('role', 'locataire')->get();

            $locataires = $allLocataires;
            if ($locataireFiltre) {
                $locataires = $allLocataires->where('id', $locataireFiltre);
            }

            $aperçus = $locataires->keyBy('id')->map(function ($locataire) use ($service, $dateDebut, $dateFin) {
                return $service->genererApercu($locataire, $dateDebut, $dateFin);
            });

            // Filtrer par statut
            if ($statutFiltre) {
                $locataires = $locataires->filter(function ($locataire) use ($aperçus, $statutFiltre) {
                    $apercu = $aperçus[$locataire->id] ?? null;
                    if (!$apercu) return false;
                    return ($apercu['statut_global']['code'] ?? 'aucun') === $statutFiltre;
                });
                $aperçus = $aperçus->only($locataires->pluck('id'));
            }

            // KPI globaux
            $kpiGlobal = [
                'total_du' => $aperçus->sum('total_du'),
                'total_paye' => $aperçus->sum('total_paye'),
                'total_restant' => $aperçus->sum('total_restant'),
                'total_en_retard' => $aperçus->sum('montant_retard'),
                'nb_en_retard' => $aperçus->sum('nb_en_retard'),
                'nb_impayees' => $aperçus->sum('nb_impayees'),
            ];

            return view('backend.pages.rapports.locataire-select', compact(
                'locataires',
                'allLocataires',
                'aperçus',
                'dateDebut',
                'dateFin',
                'kpiGlobal'
            ));
        }

        // Détail d'un locataire
        $locataire = User::findOrFail($locataireId);
        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : null;

        $rapport = $service->genererRapport($locataire, $dateDebut, $dateFin);
        $locataires = User::where('role', 'locataire')->get();

        return view('backend.pages.rapports.locataire', compact(
            'rapport',
            'locataire',
            'dateDebut',
            'dateFin',
            'locataires'
        ));
    }

    /**
     * Télécharger rapport locataire en PDF
     */
    public function telechargerRapportLocataire(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $locataireId = $request->input('locataire_id');
        if (!$locataireId) {
            return redirect()->back()->with('error', 'Locataire non spécifié');
        }

        $locataire = User::findOrFail($locataireId);
        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : null;

        $service = new RapportLocataireService();
        $rapport = $service->genererRapport($locataire, $dateDebut, $dateFin);

        $pdf = Pdf::loadView('backend.pages.rapports.pdf.locataire-rapport', compact(
            'rapport',
            'locataire',
            'dateDebut',
            'dateFin'
        ))->setPaper('a4', 'portrait');

        $nomFichier = 'rapport_locataire_' . str_replace(' ', '_', $locataire->username)
            . ($dateDebut ? '_' . $dateDebut->format('Ymd') : '')
            . ($dateFin ? '_' . $dateFin->format('Ymd') : '')
            . '.pdf';
        return $pdf->download($nomFichier);
    }

    /**
     * Télécharger rapport global locataires en PDF
     */
    public function telechargerRapportLocataireGlobal(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : null;

        $locataires = User::where('role', 'locataire')
            ->whereHas('locations', function ($q) {
                $q->whereIn('statut', ['actif', 'resilie', 'en_attente_paiement']);
            })->get();

        $service = new RapportLocataireService();
        $aperçus = $locataires->keyBy('id')->map(function ($locataire) use ($service, $dateDebut, $dateFin) {
            return $service->genererApercu($locataire, $dateDebut, $dateFin);
        });

        $kpiGlobal = [
            'total_du' => $aperçus->sum('total_du'),
            'total_paye' => $aperçus->sum('total_paye'),
            'total_restant' => $aperçus->sum('total_restant'),
            'total_en_retard' => $aperçus->sum('montant_retard'),
            'nb_en_retard' => $aperçus->sum('nb_en_retard'),
            'nb_impayees' => $aperçus->sum('nb_impayees'),
        ];

        $pdf = Pdf::loadView('backend.pages.rapports.pdf.locataire-select-rapport', compact(
            'locataires',
            'aperçus',
            'dateDebut',
            'dateFin',
            'kpiGlobal'
        ))->setPaper('a4', 'landscape');

        $nomFichier = 'rapport_global_locataires'
            . ($dateDebut ? '_' . $dateDebut->format('Ymd') : '')
            . ($dateFin ? '_' . $dateFin->format('Ymd') : '')
            . '.pdf';
        return $pdf->download($nomFichier);
    }

    // =====================================================
    // RAPPORT ACHETEURS
    // =====================================================

    /**
     * Rapport acheteurs - Liste ou détail
     */
    public function rapportAcheteur(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'acheteur_id' => 'nullable|exists:users,id',
        ]);

        $acheteurId = $request->input('acheteur_id');
        $service = new RapportAcheteurService();

        if (!$acheteurId) {
            // Liste de tous les acheteurs
            $acheteurFiltre = $request->input('acheteur_filtre');
            $statutFiltre = $request->input('statut_filtre');
            $dateDebut = $request->input('date_debut')
                ? Carbon::parse($request->input('date_debut'))->startOfDay()
                : null;
            $dateFin = $request->input('date_fin')
                ? Carbon::parse($request->input('date_fin'))->endOfDay()
                : null;

            $allAcheteurs = User::where('role', 'acheteur')
                ->whereHas('ventes', function ($q) {
                    $q->whereIn('statut', ['offre_acceptee', 'terminee']);
                })->get();

            $acheteurs = $allAcheteurs;
            if ($acheteurFiltre) {
                $acheteurs = $allAcheteurs->where('id', $acheteurFiltre);
            }

            $aperçus = $acheteurs->keyBy('id')->map(function ($acheteur) use ($service, $dateDebut, $dateFin) {
                return $service->genererApercu($acheteur, $dateDebut, $dateFin);
            });

            // Filtrer par statut
            if ($statutFiltre) {
                $acheteurs = $acheteurs->filter(function ($acheteur) use ($aperçus, $statutFiltre) {
                    $apercu = $aperçus[$acheteur->id] ?? null;
                    if (!$apercu) return false;
                    return ($apercu['statut_global']['code'] ?? 'aucun') === $statutFiltre;
                });
                $aperçus = $aperçus->only($acheteurs->pluck('id'));
            }

            // KPI globaux
            $kpiGlobal = [
                'total_a_payer' => $aperçus->sum('total_a_payer'),
                'total_paye' => $aperçus->sum('total_paye'),
                'total_restant' => $aperçus->sum('total_restant'),
                'total_paye_periode' => $aperçus->sum('total_paye_periode'),
            ];


            return view('backend.pages.rapports.acheteur-select', compact(
                'acheteurs',
                'allAcheteurs',
                'aperçus',
                'dateDebut',
                'dateFin',
                'kpiGlobal'
            ));
        }

        // Détail d'un acheteur
        $acheteur = User::findOrFail($acheteurId);
        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : null;

        $rapport = $service->genererRapport($acheteur, $dateDebut, $dateFin);
        $acheteurs = User::where('role', 'acheteur')->get();

        return view('backend.pages.rapports.acheteur', compact(
            'rapport',
            'acheteur',
            'dateDebut',
            'dateFin',
            'acheteurs'
        ));
    }

    /**
     * Télécharger rapport acheteur en PDF
     */
    public function telechargerRapportAcheteur(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $acheteurId = $request->input('acheteur_id');
        if (!$acheteurId) {
            return redirect()->back()->with('error', 'Acheteur non spécifié');
        }

        $acheteur = User::findOrFail($acheteurId);
        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : null;

        $service = new RapportAcheteurService();
        $rapport = $service->genererRapport($acheteur, $dateDebut, $dateFin);

        $pdf = Pdf::loadView('backend.pages.rapports.pdf.acheteur-rapport', compact(
            'rapport',
            'acheteur',
            'dateDebut',
            'dateFin'
        ))->setPaper('a4', 'portrait');

        $nomFichier = 'rapport_acheteur_' . str_replace(' ', '_', $acheteur->username) . '_' . ($dateDebut ? $dateDebut->format('Ymd') : 'debut') . '_' . ($dateFin ? $dateFin->format('Ymd') : 'fin') . '.pdf';
        return $pdf->download($nomFichier);
    }

    /**
     * Télécharger rapport global acheteurs en PDF
     */
    public function telechargerRapportAcheteurGlobal(Request $request)
    {
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $dateDebut = $request->input('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->input('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : null;

        $acheteurs = User::where('role', 'acheteur')
            ->whereHas('ventes', function ($q) {
                $q->whereIn('statut', ['offre_acceptee', 'terminee']);
            })->get();

        $service = new RapportAcheteurService();
        $aperçus = $acheteurs->keyBy('id')->map(function ($acheteur) use ($service, $dateDebut, $dateFin) {
            return $service->genererApercu($acheteur, $dateDebut, $dateFin);
        });

        $kpiGlobal = [
            'total_a_payer' => $aperçus->sum('total_a_payer'),
            'total_paye' => $aperçus->sum('total_paye'),
            'total_restant' => $aperçus->sum('total_restant'),
            'total_paye_periode' => $aperçus->sum('total_paye_periode'),
        ];

        $pdf = Pdf::loadView('backend.pages.rapports.pdf.acheteur-select-rapport', compact(
            'acheteurs',
            'aperçus',
            'dateDebut',
            'dateFin',
            'kpiGlobal'
        ))->setPaper('a4', 'landscape');

        $nomFichier = 'rapport_global_acheteurs_' . ($dateDebut ? $dateDebut->format('Ymd') : 'debut') . '_' . ($dateFin ? $dateFin->format('Ymd') : 'fin') . '.pdf';
        return $pdf->download($nomFichier);
    }



    // =====================================================
    // GESTION DES CHARGES
    // =====================================================

    /**
     * Gestion des charges - Liste des charges
     */
    public function chargesIndex(Request $request)
    {
        // Seul les administrateurs peuvent voir les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $query = Charge::with('annonce');

        // Filtre par bien
        if ($request->filled('annonce_id')) {
            $query->where('annonce_id', $request->input('annonce_id'));
        }

        // Filtre par type
        if ($request->filled('type_charge')) {
            $query->where('type_charge', $request->input('type_charge'));
        }

        // Filtre par date
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_charge', [
                $request->input('date_debut'),
                $request->input('date_fin')
            ]);
        }

        $charges = $query->orderBy('date_charge', 'desc')->get();

        // Liste des biens
        $biens = \App\Models\Annonce::all();

        return view('backend.pages.rapports.charges.index', compact(
            'charges',
            'biens'
        ));
    }





    /**
     * Gestion des charges - Créer une charge
     */
    public function chargesCreate(Request $request)
    {
        // Seul les administrateurs peuvent créer des charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $biens = Annonce::all();

        return view('backend.pages.rapports.charges.create', compact('biens'));
    }

    /**
     * Gestion des charges - Enregistrer une charge
     */
    public function chargesStore(Request $request)
    {
        // Seul les administrateurs peuvent créer des charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'type_charge' => 'required|in:maintenance,reparation,taxe,autre',
            'montant' => 'required|numeric|min:0',
            'date_charge' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);
        //generer la reference si elle n'est pas fournie
        if (!$request->input('reference')) {
            $request->merge([
                'reference' => 'FAC-' . strtoupper(Str::random(8))
            ]);
        }

        Charge::create($request->all());

        if ($request->input('_redirect_back')) {
            return redirect()->back()->with('success', 'Charge enregistrée avec succès');
        }

        return redirect()->route('backend.charges.index')
            ->with('success', 'Charge enregistrée avec succès');
    }

    /**
     * Gestion des charges - Éditer une charge
     */
    public function chargesEdit(Charge $charge)
    {
        // Seul les administrateurs peuvent éditer les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $biens = Annonce::all();

        return view('backend.pages.rapports.charges.edit', compact('charge', 'biens'));
    }

    /**
     * Gestion des charges - Mettre à jour une charge
     */
    public function chargesUpdate(Request $request, Charge $charge)
    {
        // Seul les administrateurs peuvent mettre à jour les charges
        if (in_array(Auth::user()->role, ['client', 'proprietaire', 'acheteur', 'locataire'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'type_charge' => 'required|in:maintenance,reparation,taxe,autre',
            'montant' => 'required|numeric|min:0',
            'date_charge' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        //generer la reference si elle n'est pas fournie
        if (!$request->input('reference')) {
            $request->merge([
                'reference' => 'FAC-' . strtoupper(Str::random(8))
            ]);
        }

        $charge->update($request->all());

        return redirect()->route('backend.charges.index')
            ->with('success', 'Charge mise à jour avec succès');
    }

    /**
     * Gestion des charges - Supprimer une charge
     */
    public function chargesDestroy(Charge $charge)
    {

        $charge->delete();

        return response()->json([
            'status' => 200
        ], 200);
    }
}
