<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Vente;

class NouvelleDemandeVente extends Notification
{
    use Queueable;

    protected $vente;

    public function __construct(Vente $vente)
    {
        $this->vente = $vente;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'demande_vente',
            'vente_id' => $this->vente->id,
            'client_nom' => $this->vente->client->name,
            'bien_titre' => $this->vente->annonce->titre,
            'prix' => $this->vente->prix_vente,
            'message' => 'Nouvelle demande de vente de ' . $this->vente->client->name . ' pour ' . $this->vente->annonce->titre,
            'url' => route('backend.ventes.show', $this->vente->id),
        ];
    }
}
