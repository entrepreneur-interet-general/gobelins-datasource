<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationState extends Model
{
    protected $table = 'diffusion';
    protected $primaryKey = 'numdiffusion';

    const PUBLICATION_MAP = [
        '?' => true,        // À définir
        'P' => true,        // Publiable (données minimum)
        'P+D' => true,      // Publiable + description
        'P+D+P' => true,    // ??? Not sure this still exists
        'P+D+O' => true,    // Publiable + description + origine détail
        'NP-R' => false,    // Non publiable > radié
        'NP-MV' => false,   // Non publiable > mauvais état
        'NP-NL' => false,   // Non publiable > non localisé
        'NP-U' => false,    // Non publiable > usuel
        'NP-&c' => false,   // Non publiable > autre raison
        'NP-null' => false, // ??? Not sure this still exists
    ];

    // Eloquent relationships

    public function products()
    {
        return $this->hasMany(Product::class, 'numdiffusion');
    }
}
