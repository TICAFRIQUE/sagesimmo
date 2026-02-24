<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPermissions, HasRoles, SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [

        'username',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'role',
        'commercial_id', // Pour les propriétaires, le commercial qui les gère
        'created_at',
        'updated_at',
        'deleted_at',

    ];


    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = IdGenerator::generate(['table' => 'users', 'length' => 10, 'prefix' =>
            mt_rand()]);
        });
    }



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Register media collections for the user
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->useFallbackUrl(asset('build/images/users/avatar-default.png'))
            ->useFallbackPath(public_path('build/images/users/avatar-default.png'));

        $this->addMediaCollection('piece_identite')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    /**
     * Relation avec les locations (en tant que locataire)
     */
    public function locations()
    {
        return $this->hasMany(Location::class, 'locataire_id');
    }

    /**
     * Relation avec les ventes (en tant que client)
     */
    public function ventes()
    {
        return $this->hasMany(Vente::class, 'client_id');
    }

    /**
     * Relation avec les annonces (en tant que propriétaire)
     */
    public function annonces()
    {
        return $this->hasMany(Annonce::class, 'proprietaire_id');
    }

    /**
     * Reelation pour recuperer le commercial qui gère ce propriétaire
     */
    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    /**
     * Scope pour récupérer uniquement les propriétaires
     */
    public function scopeProprietaires($query)
    {
        return $query->role('proprietaire');
    }

    /**
     * Scope pour récupérer uniquement les commerciaux
     */
    public function scopeCommerciaux($query)
    {
        return $query->role('commerciale');
    }

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getNameAttribute()
    {
        return $this->username;
    }


}
