<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pret extends Model
{
    protected $fillable = [
        'date_echeant',
        'montant_total',
        'client_id',
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
