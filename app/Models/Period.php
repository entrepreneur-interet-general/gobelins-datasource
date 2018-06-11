<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'epo';
    protected $primaryKey = 'numepo';


    // Eloquent relationships

    public function products()
    {
        return $this->hasMany(Product::class, 'numepo');
    }


    // Accessors

    public function getNameAttribute()
    {
        return trim($this->epo);
    }

    public function getStartYearAttribute()
    {
        preg_match('/\(([0-9]{4})\-/', $this->epo, $matches);
        return intval($matches[1]);
    }
    
    public function getEndYearAttribute()
    {
        preg_match('/\-([0-9]{4})\)/', $this->epo, $matches);
        return intval($matches[1]);
    }
}
