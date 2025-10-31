<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant_payer',
        'mode_paiement',
        'pret_id',
    ];

    protected function casts(): array
    {
        return [
            'montant_payer' => 'float',
        ];
    }

    public function pret(): BelongsTo
    {
        return $this->belongsTo(Pret::class);
    }

    /**
     * Boot method pour mettre à jour le montant_rest du prêt lors des opérations CRUD
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($paiement) {
            $paiement->pret->mettreAJourMontantRestant();
        });

        static::updated(function ($paiement) {
            $paiement->pret->mettreAJourMontantRestant();
        });

        static::deleted(function ($paiement) {
            $paiement->pret->mettreAJourMontantRestant();
        });
    }
}
