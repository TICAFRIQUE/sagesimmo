<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Cviebrock\EloquentSluggable\Sluggable;

class Annonce extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Sluggable;

    protected $fillable = [
        'titre',
        'description',
        'type_transaction',
        'type_bien_id',
        'prix',
        'commission',
        'type_commission',
        'surface',
        'nombre_chambres',
        'nombre_salles_bain',
        'nombre_pieces',
        'etage',
        'adresse',
        'ville',
        'commune',
        'quartier',
        'code_postal',
        'latitude',
        'longitude',
        'statut',
        'en_vedette',
        'date_disponibilite',
        'annee_construction',
        'caracteristiques_supplementaires',
        'reference',
        'proprietaire_id',
        'est_bien_agence',
        'created_by_id',
        'nombre_vues',
    ];

    protected $casts = [
        'prix' => 'integer',
        'commission' => 'integer',
        'surface' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'en_vedette' => 'boolean',
        'est_bien_agence' => 'boolean',
        'date_disponibilite' => 'date',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'titre'
            ]
        ];
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');

        $this->addMediaCollection('image_principale')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('documents')
            ->useDisk('public');
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Conversion miniature
        $this->addMediaConversion('thumb')
            ->performOnCollections('images', 'image_principale')
            ->width(400)
            ->height(300)
            ->nonQueued();
    }

    /**
     * Relation avec le propriétaire du bien
     */
    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé l'annonce (peut être un agent, admin, etc.)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Relation avec l'utilisateur (compatibilité)
     * @deprecated Utilisez proprietaire() ou createdBy() à la place
     */
    public function user()
    {
        return $this->proprietaire();
    }

    /**
     * Relation avec le type de bien
     */
    public function typeBien()
    {
        return $this->belongsTo(TypeBien::class, 'type_bien_id');
    }

    /**
     * Relation avec les équipements
     */
    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'annonce_equipement');
    }

    /**
     * Relation avec les locations
     */
    public function locations()
    {
        return $this->hasMany(Location::class, 'annonce_id');
    }

    /**
     * Relation avec les ventes
     */
    public function ventes()
    {
        return $this->hasMany(Vente::class, 'annonce_id');
    }

    /**
     * Relation avec les charges (maintenance, réparation, taxes, etc.)
     */
    public function charges()
    {
        return $this->hasMany(Charge::class, 'annonce_id');
    }

    /**
     * Scope pour filtrer par type de transaction
     */
    public function scopeVente($query)
    {
        return $query->where('type_transaction', 'vente');
    }

    public function scopeLocation($query)
    {
        return $query->where('type_transaction', 'location');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'disponible');
    }

    /**
     * Scope pour les annonces en vedette
     */
    public function scopeEnVedette($query)
    {
        return $query->where('en_vedette', true);
    }

    /**
     * Scope pour les biens de l'agence
     */
    public function scopeBienAgence($query)
    {
        return $query->where('est_bien_agence', true);
    }

    /**
     * Scope pour les biens de propriétaires externes
     */
    public function scopeBienExterne($query)
    {
        return $query->where('est_bien_agence', false);
    }

    /**
     * Générer une référence unique
     */
    public static function genererReference()
    {
        do {
            $reference = 'ANN-' . strtoupper(uniqid());
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Formater le prix
     */
    public function getPrixFormate()
    {
        return number_format($this->prix, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir le nom du propriétaire ou "Agence"
     */
    public function getNomProprietaireAttribute()
    {
        if ($this->est_bien_agence) {
            return 'Agence';
        }
        
        return $this->proprietaire ? $this->proprietaire->name : 'N/A';
    }

    /**
     * Vérifier si le bien appartient à l'agence
     */
    public function appartientAgence()
    {
        return $this->est_bien_agence;
    }
}
