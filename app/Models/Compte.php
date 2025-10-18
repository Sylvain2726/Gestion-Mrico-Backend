<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    protected $fillable = [
        'num_compte',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(User::class);
    }
}
