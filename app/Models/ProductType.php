<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'gracat';
    protected $primaryKey = 'numgraca';

    public function products()
    {
        return $this->hasMany(Product::class, 'numgraca');
    }
}
