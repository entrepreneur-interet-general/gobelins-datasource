<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'mat';
    protected $primaryKey = 'codmat';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'objmat', 'codmat', 'obj_id');
    }
}
