<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Location;

class NouvelleDemandeLocation extends Notification
{
    use Queueable;

    protected $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'demande_location',
            'location_id' => $this->location->id,
            'locataire_nom' => $this->location->locataire->name,
            'bien_titre' => $this->location->annonce->titre,
            'loyer' => $this->location->loyer_mensuel,
            'message' => 'Nouvelle demande de location de ' . $this->location->locataire->name . ' pour ' . $this->location->annonce->titre,
            'url' => route('backend.locations.show', $this->location->id),
        ];
    }
}
