<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyInventoryNumber extends Model
{
    protected $table = 'ancnum';
    protected $primaryKey = 'ancnum';

    public function products()
    {
        return $this->belongsTo(Product::class, 'obj_id');
    }
}
