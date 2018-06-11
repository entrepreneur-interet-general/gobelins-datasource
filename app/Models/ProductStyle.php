<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStyle extends Model
{
    protected $table = 'sty';
    protected $primaryKey = 'numsty';

    public function products()
    {
        return $this->hasMany(Product::class, 'numsty');
    }
}
