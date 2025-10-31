<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pret extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_echeant',
        'montant_total',
        'montant_rest',
        'client_id',
    ];

    protected function casts(): array
    {
        return [
            'montant_total' => 'float',
            'montant_rest' => 'float',
            'date_echeant' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id')->where('type', 'client');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Calcule le montant restant du prêt basé sur les paiements effectués
     */
    public function calculerMontantRestant(): float
    {
        $totalPaiements = $this->paiements()->sum('montant_payer');

        return $this->montant_total - $totalPaiements;
    }

    /**
     * Met à jour le montant restant du prêt
     */
    public function mettreAJourMontantRestant(): void
    {
        $this->update(['montant_rest' => $this->calculerMontantRestant()]);
    }

    /**
     * Boot method pour initialiser le montant_rest lors de la création
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($pret) {
            if (is_null($pret->montant_rest)) {
                $pret->montant_rest = $pret->montant_total;
            }
        });
    }
}
