<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upholstery extends Model
{
    protected $table = 'gar';
    protected $primaryKey = 'codgar';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'objgar', 'codgar', 'obj_id');
    }
}
