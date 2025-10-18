<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

// Le Client hérite de User
class Client extends User
{
    // Indique explicitement que ce modèle utilise la table 'users'
    protected $table = 'users';



    protected $fillable = [
        'name',
        'email',
        'tel',
        // 'password' est exclu ici
    ];

    /**
     * The "booted" method of the model.
     * C'est l'endroit propre pour ajouter des scopes globaux et des hooks.
     *
     * @return void
     */
    protected static function booted()
    {
        //static::removeGlobalScope('user');

        // 1. Scope Global pour filtrer uniquement les clients
        static::addGlobalScope('client', function (Builder $builder) {
            $builder->where('type', 'client');
        });


        // 2. Hook de création pour définir le type et le mot de passe
        static::creating(function (self $client) {
            // Définit le type pour tous les nouveaux enregistrements de Client
            $client->type = 'client';

            // IMPORTANT : S'assure que le mot de passe est null si non fourni
            // (Nécessite que la colonne 'password' soit NULLABLE dans la DB)
            if (empty($client->password)) {
                $client->password = null;
            }
        });
    }



    public function prets()
    {
        return $this->hasMany(Pret::class);
    }
}
