<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationState extends Model
{
    protected $table = 'diffusion';
    protected $primaryKey = 'numdiffusion';

    const PUBLICATION_MAP = [
        '?' => true,
        'P' => true,
        'P+D' => true,
        'P+D+P' => true,
        'NP-R' => false,
        'NP-MV' => false,
        'NP-NL' => false,
        'NP-U' => false,
        'NP-&c' => false,
    ];


    // Eloquent relationships

    public function products()
    {
        return $this->hasMany(Product::class, 'numdiffusion');
    }
}
