# 🧪 Testing & Validation - Module Rapport Financier

## Tests Unitaires

### RapportProprietaireServiceTest

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Location;
use App\Models\Paiement;
use App\Models\Charge;
use App\Services\RapportProprietaireService;
use Carbon\Carbon;

class RapportProprietaireServiceTest extends TestCase
{
    protected $proprietaire;
    protected $service;
    protected $dateDebut;
    protected $dateFin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RapportProprietaireService();
        $this->proprietaire = User::factory()->create(['role' => 'proprietaire']);
        $this->dateDebut = Carbon::parse('2025-01-01');
        $this->dateFin = Carbon::parse('2025-12-31');
    }

    /**
     * Test génération rapport avec 0 bien
     */
    public function test_rapport_avec_zero_bien()
    {
        $rapport = $this->service->genererRapport(
            $this->proprietaire,
            $this->dateDebut,
            $this->dateFin
        );

        $this->assertEquals(0, $rapport['nombre_biens']);
        $this->assertEquals(0, $rapport['total_brut_encaisse']);
        $this->assertEquals(0, $rapport['revenue_net']);
    }

    /**
     * Test rapport avec 1 bien et loyers
     */
    public function test_rapport_avec_un_bien_et_loyers()
    {
        // Créer annonce
        $annonce = Annonce::factory()->create([
            'proprietaire_id' => $this->proprietaire->id,
        ]);

        // Créer location
        $location = Location::factory()->create([
            'annonce_id' => $annonce->id,
            'commission_agence' => 50000,
        ]);

        // Créer paiement loyer
        Paiement::factory()->create([
            'payable_type' => Location::class,
            'payable_id' => $location->id,
            'type_paiement' => 'loyer',
            'montant' => 1000000,
            'status' => 'paye',
            'commission_agence' => 50000,
            'date_paiement' => $this->dateDebut->addDays(5),
        ]);

        $rapport = $this->service->genererRapport(
            $this->proprietaire,
            $this->dateDebut,
            $this->dateFin
        );

        $this->assertEquals(1, $rapport['nombre_biens']);
        $this->assertEquals(1000000, $rapport['total_brut_encaisse']);
        $this->assertEquals(50000, $rapport['total_commission_agence']);
    }

    /**
     * Test déduction des charges
     */
    public function test_rapport_avec_charges()
    {
        $annonce = Annonce::factory()->create([
            'proprietaire_id' => $this->proprietaire->id,
        ]);

        // Créer charges
        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'type_charge' => 'maintenance',
            'montant' => 100000,
            'date_charge' => $this->dateDebut->addDays(3),
        ]);

        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'type_charge' => 'reparation',
            'montant' => 50000,
            'date_charge' => $this->dateDebut->addDays(10),
        ]);

        $rapport = $this->service->genererRapport(
            $this->proprietaire,
            $this->dateDebut,
            $this->dateFin
        );

        $this->assertEquals(150000, $rapport['total_charges']);
        $this->assertEquals(2, $rapport['detail_charges']['nombre_charges']);
    }

    /**
     * Test calcul revenu net correct
     */
    public function test_calcul_revenu_net()
    {
        $annonce = Annonce::factory()->create([
            'proprietaire_id' => $this->proprietaire->id,
        ]);

        $location = Location::factory()->create([
            'annonce_id' => $annonce->id,
            'commission_agence' => 10,
            'type_commission' => 'pourcentage',
        ]);

        // 1M loyer avec 10% commission = 100k
        Paiement::factory()->create([
            'payable_type' => Location::class,
            'payable_id' => $location->id,
            'type_paiement' => 'loyer',
            'montant' => 1000000,
            'status' => 'paye',
            'commission_agence' => 10,
            'type_commission' => 'pourcentage',
        ]);

        // 50k charge maintanance
        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'type_charge' => 'maintenance',
            'montant' => 50000,
            'date_charge' => $this->dateDebut,
        ]);

        $rapport = $this->service->genererRapport(
            $this->proprietaire,
            $this->dateDebut,
            $this->dateFin
        );

        // 1M - 100k (commission) - 50k (charge) = 850k
        $this->assertEquals(850000, $rapport['revenue_net']);
    }

    /**
     * Test filtrage par dates
     */
    public function test_rapport_filtrage_dates()
    {
        $annonce = Annonce::factory()->create([
            'proprietaire_id' => $this->proprietaire->id,
        ]);

        // Paiement dans la période
        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'montant' => 50000,
            'date_charge' => $this->dateDebut->addDays(5),
        ]);

        // Paiement avant la période
        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'montant' => 100000,
            'date_charge' => $this->dateDebut->subDays(10),
        ]);

        // Paiement après la période
        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'montant' => 75000,
            'date_charge' => $this->dateFin->addDays(10),
        ]);

        $rapport = $this->service->genererRapport(
            $this->proprietaire,
            $this->dateDebut,
            $this->dateFin
        );

        // Seule la charge dans la période compte
        $this->assertEquals(50000, $rapport['total_charges']);
    }
}
```

### RapportAgenceServiceTest

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Location;
use App\Models\Vente;
use App\Models\Paiement;
use App\Services\RapportAgenceService;
use Carbon\Carbon;

class RapportAgenceServiceTest extends TestCase
{
    protected $service;
    protected $dateDebut;
    protected $dateFin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RapportAgenceService();
        $this->dateDebut = Carbon::parse('2025-01-01');
        $this->dateFin = Carbon::parse('2025-12-31');
    }

    /**
     * Test rapport vide
     */
    public function test_rapport_agence_vide()
    {
        $rapport = $this->service->genererRapport(
            $this->dateDebut,
            $this->dateFin
        );

        $this->assertEquals(0, $rapport['total_encaisse']);
        $this->assertEquals(0, $rapport['total_commissions']);
    }

    /**
     * Test commissions loyers
     */
    public function test_commissions_loyers()
    {
        $proprietaire = User::factory()->create();
        $annonce = Annonce::factory()->create(['proprietaire_id' => $proprietaire->id]);
        
        $location = Location::factory()->create([
            'annonce_id' => $annonce->id,
            'commission_agence' => 5,
            'type_commission' => 'pourcentage',
        ]);

        // 2 loyers de 500k chacun = 1M
        Paiement::factory(2)->create([
            'payable_type' => Location::class,
            'payable_id' => $location->id,
            'type_paiement' => 'loyer',
            'montant' => 500000,
            'status' => 'paye',
            'commission_agence' => 5,
            'type_commission' => 'pourcentage',
            'date_paiement' => $this->dateDebut->addDays(10),
        ]);

        $rapport = $this->service->genererRapport(
            $this->dateDebut,
            $this->dateFin
        );

        // 1M loyers * 5% = 50k commission
        $this->assertEquals(1000000, $rapport['total_loyers_encaisses']);
        $this->assertEquals(50000, $rapport['commissions_loyers']);
    }

    /**
     * Test commissions ventes
     */
    public function test_commissions_ventes()
    {
        $proprietaire = User::factory()->create();
        $annonce = Annonce::factory()->create(['proprietaire_id' => $proprietaire->id]);
        
        $vente = Vente::factory()->create([
            'annonce_id' => $annonce->id,
            'prix_vente' => 500000000,
            'commission_agence' => 3,
            'type_commission' => 'pourcentage',
        ]);

        Paiement::factory()->create([
            'payable_type' => Vente::class,
            'payable_id' => $vente->id,
            'montant' => 500000000,
            'status' => 'paye',
            'commission_agence' => 3,
            'type_commission' => 'pourcentage',
            'date_paiement' => $this->dateDebut->addDays(5),
        ]);

        $rapport = $this->service->genererRapport(
            $this->dateDebut,
            $this->dateFin
        );

        // 500M * 3% = 15M commission
        $this->assertEquals(500000000, $rapport['total_ventes_encaissees']);
        $this->assertEquals(15000000, $rapport['commissions_ventes']);
    }

    /**
     * Test revenu agence = total commissions
     */
    public function test_revenu_agence()
    {
        // Créer transactions loyer
        $proprietaire = User::factory()->create();
        $annonce = Annonce::factory()->create(['proprietaire_id' => $proprietaire->id]);
        
        $location = Location::factory()->create(['annonce_id' => $annonce->id]);
        Paiement::factory()->create([
            'payable_type' => Location::class,
            'payable_id' => $location->id,
            'type_paiement' => 'loyer',
            'montant' => 1000000,
            'status' => 'paye',
            'commission_agence' => 50000,
        ]);

        // Créer transactions vente
        $vente = Vente::factory()->create(['annonce_id' => $annonce->id]);
        Paiement::factory()->create([
            'payable_type' => Vente::class,
            'payable_id' => $vente->id,
            'montant' => 500000000,
            'status' => 'paye',
            'commission_agence' => 10000000,
        ]);

        $rapport = $this->service->genererRapport(
            $this->dateDebut,
            $this->dateFin
        );

        // Total = 50k + 10M = 10.05M
        $expected = 50000 + 10000000;
        $this->assertEquals($expected, $rapport['total_commissions']);
    }
}
```

---

## Tests Fonctionnels (Feature Tests)

### RapportProprietaireControllerTest

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Location;
use App\Models\Charge;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RapportProprietaireControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test accès non authentifié
     */
    public function test_non_authentifie_redirection()
    {
        $response = $this->get('/admin/rapports/proprietaire');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test propriétaire voit son rapport
     */
    public function test_proprietaire_voit_son_rapport()
    {
        $proprietaire = User::factory()->create(['role' => 'proprietaire']);
        $annonce = Annonce::factory()->create(['proprietaire_id' => $proprietaire->id]);

        $response = $this->actingAs($proprietaire)
            ->get('/admin/rapports/proprietaire');

        $response->assertStatus(200);
        $response->assertViewIs('backend.pages.rapports.proprietaire');
        $response->assertViewHas('rapport');
    }

    /**
     * Test admin voit tous les rapports
     */
    public function test_admin_voit_tous_rapports()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $proprietaire = User::factory()->create(['role' => 'proprietaire']);

        $response = $this->actingAs($admin)
            ->get('/admin/rapports/proprietaire?proprietaire_id=' . $proprietaire->id);

        $response->assertStatus(200);
    }

    /**
     * Test affichage charges dans rapport
     */
    public function test_affichage_charges_rapport()
    {
        $proprietaire = User::factory()->create(['role' => 'proprietaire']);
        $annonce = Annonce::factory()->create(['proprietaire_id' => $proprietaire->id]);
        
        Charge::factory()->create([
            'annonce_id' => $annonce->id,
            'montant' => 100000,
        ]);

        $response = $this->actingAs($proprietaire)
            ->get('/admin/rapports/proprietaire');

        $response->assertViewHas('rapport');
        $this->assertEquals(100000, $response->viewData('rapport')['total_charges']);
    }
}
```

### ChargesControllerTest

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Charge;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChargesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test créer charge
     */
    public function test_creer_charge()
    {
        $proprietaire = User::factory()->create(['role' => 'proprietaire']);
        $annonce = Annonce::factory()->create(['proprietaire_id' => $proprietaire->id]);

        $response = $this->actingAs($proprietaire)
            ->post('/admin/charges', [
                'annonce_id' => $annonce->id,
                'type_charge' => 'maintenance',
                'montant' => 75000,
                'date_charge' => '2025-02-15',
                'description' => 'Ramonage cheminée',
            ]);

        $response->assertRedirect('/admin/charges');
        $this->assertDatabaseHas('charges', [
            'annonce_id' => $annonce->id,
            'montant' => 75000,
        ]);
    }

    /**
     * Test validation obligatoire
     */
    public function test_validation_charges()
    {
        $proprietaire = User::factory()->create(['role' => 'proprietaire']);

        $response = $this->actingAs($proprietaire)
            ->post('/admin/charges', []);

        $response->assertSessionHasErrors(['annonce_id', 'type_charge', 'montant', 'date_charge']);
    }

    /**
     * Test propriétaire ne peut pas éditer charge d'autrui
     */
    public function test_proprietaire_acces_negatif()
    {
        $prop1 = User::factory()->create(['role' => 'proprietaire']);
        $prop2 = User::factory()->create(['role' => 'proprietaire']);
        
        $annonce = Annonce::factory()->create(['proprietaire_id' => $prop1->id]);
        $charge = Charge::factory()->create(['annonce_id' => $annonce->id]);

        $response = $this->actingAs($prop2)
            ->delete('/admin/charges/' . $charge->id);

        $response->assertStatus(302); // Redirection
        $this->assertDatabaseHas('charges', ['id' => $charge->id]); // Pas supprimée
    }
}
```

---

## Cas de Test Manuels

### Scénario 1 : Reporter d'un propriétaire simple

```
1. Créer un propriétaire "Sidy Diallo"
2. Créer un bien "Maison à Dakar" (500k/mois)
3. Enregistrer 12 paiements loyers de 500k
4. Enregistrer charges:
   - 2025-01-15: Maintenance 75k
   - 2025-06-10: Réparation 150k
   - 2025-12-01: Taxe 50k
5. Rapport propriétaire 2025:
   ✓ Total Brut: 6 000 000 F
   ✓ Commission (10%): 600 000 F
   ✓ Charges: 275 000 F
   ✓ Revenu Net: 5 125 000 F
```

### Scénario 2 : Rapport agence multi-propriétaires

```
1. Créer 3 propriétaires avec 2 biens chacun
2. Enregistrer transactions variées
3. Rapport agence:
   ✓ Total Loyers: XX
   ✓ Total Ventes: XX
   ✓ Commissions Loyers: XX
   ✓ Commissions Ventes: XX
   ✓ Revenu Total Agence: XX
   ✓ Top propriétaires affichés
   ✓ Top biens affichés
```

### Scénario 3 : Filtres par date

```
Créer charges sur plusieurs mois
Filtrer Janvier-Mars: ✓ Voir 3 charges
Filtrer Juillet: ✓ Voir 1 charge
Filtrer Novembre-Décembre: ✓ Voir 2 charges
```

---

## Checklist Validation

- [ ] Migration crée table charges
- [ ] Modèle Charge instanciable
- [ ] Service Propriétaire calcule correctement
- [ ] Service Agence calcule correctement
- [ ] Contrôleur ok authentification
- [ ] Propriétaire voit son rapport
- [ ] Admin voit tous rapports
- [ ] Charges déduites correctement
- [ ] Commissions calculées correctement
- [ ] Filtres date fonctionnent
- [ ] Pagination charges ok
- [ ] Edit charge fonctionne
- [ ] Delete charge fonctionne
- [ ] Validation formulaires ok
- [ ] Vues s'affichent correctement
- [ ] Impression (print) fonctionne
- [ ] Pas d'erreur SQL
- [ ] Pas d'erreur PHP
