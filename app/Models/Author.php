<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// use Illuminate\Support\Facades\DB;

class Author extends Model
{
    protected $table = 'aut';
    protected $primaryKey = 'codaut';

    public function getFullNameAttribute()
    {
        // Remove all biographical information,
        // by convention between parentesis.
        if (strpos($this->aut, '(') !== false) {
            $truncated = trim(substr($this->aut, 0, strpos($this->aut, '(')));
        } else {
            $truncated = trim($this->aut);
        }
        return $truncated;
    }

    private function splitNameSegments()
    {
        $matches = [];
        if (preg_match('/^([- A-Z]+)\b((?:[A-Z](?:\p{L}|-| )+)*)$/u', $this->full_name, $matches) === 1) {
            $this->attributes['first_name'] = trim($matches[2]);
            $this->attributes['last_name'] = trim($matches[1]);
        } else {
            $this->attributes['first_name'] = '';
            $this->attributes['last_name'] = $this->fullName;
        }
    }
    
    public function getFirstNameAttribute()
    {
        $this->splitNameSegments();
        return $this->attributes['first_name'];
    }
        
    public function getLastNameAttribute()
    {
        $this->splitNameSegments();
        return $this->attributes['last_name'];
    }
    
    public function getYearOfBirthAttribute()
    {
        if ($this->datnaiss) {
            return substr((string) $this->datnaiss, 0, 4);
        } else {
            // First group of 4 numbers
            if (preg_match('/.+?([0-9]{4}).*/u', $this->aut, $matches) === 1) {
                return $matches[1];
            } else {
                return null;
            }
        }
    }

    public function getYearOfDeathAttribute()
    {
        if ($this->datdeces) {
            return substr((string) $this->datdeces, 0, 4);
        } else {
            // Last group of 4 numbers
            if (preg_match('/.+?[0-9]{4}.+([0-9]{4}).*/u', $this->aut, $matches) === 1) {
                return $matches[1];
            } else {
                return null;
            }
        }
    }
}
