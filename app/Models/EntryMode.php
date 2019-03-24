<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryMode extends Model
{
    protected $table = 'modeentreecollection';
    protected $primaryKey = 'nummodeentreecollection';

    public function products()
    {
        return $this->hasMany(Product::class, 'nummodeentreecollection');
    }
}
