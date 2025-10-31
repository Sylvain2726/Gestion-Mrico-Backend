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
        'lieu_naissance',
        'date_naissance',
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
        static::addGlobalScope('client', function (Builder $builder) {
            $builder->where('type', 'client');
        });

        static::creating(function (self $client) {
            $client->type = 'client';
            if (empty($client->password)) {
                $client->password = null;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function prets()
    {
        return $this->hasMany(Pret::class);
    }
}
