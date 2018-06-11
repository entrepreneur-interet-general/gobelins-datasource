<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// use Illuminate\Support\Facades\DB;

class Author extends Model
{
    protected $table = 'aut';
    protected $primaryKey = 'codaut';

    public function getLastNameAttribute()
    {
        $matches = [];
        preg_match('/([- A-Z]*)\b/', $this->aut, $matches);
        return $matches[1] ?: null;
    }


    // ^([- A-Z]+)\s*([A-Z][a-z][-A-Za-z]+)?\s*(\(.*?(\d{4}).*?(\d{4}).*?\))?$
    // public function decomposeName()
    // {
    //     // Does the name have biography details?
    //     // They are always between parentesis.
    //     if (strpos($this->name, '(') !== false) {
    //         $matches = [];
    //         $bio = preg_match('/.*(\(.*?\)).*/', $this->name, $matches);

    //     } else {
    //     }
    // }
}
