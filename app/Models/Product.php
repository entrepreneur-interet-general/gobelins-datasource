<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'obj';
    public $incrementing = false;
    protected $keyType = 'string';


    // Eloquent relationships

    public function authorships()
    {
        return $this->hasMany(Authorship::class, 'obj_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'obj_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'numepo');
    }

    public function legacyInventoryNumbers()
    {
        return $this->hasMany(LegacyInventoryNumber::class, 'obj_id');
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'numgraca');
    }

    public function productStyle()
    {
        return $this->belongsTo(ProductStyle::class, 'numsty');
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'objmat', 'obj_id', 'codmat');
    }
    

    // Scopes

    public function scopeByInventory($query, $inventory)
    {
        return $query->where('obj.id', '=', $inventory);
    }


    // Accessors and mutators

    public function getConceptionYearAttribute()
    {
        // return ($this->anncon <= 0) ? null : $this->anncon;
        return $this->anncon;
    }

    public function getDescriptionAttribute()
    {
        return Str::normalizedNewLines(trim($this->des));
    }
    
    public function getBibliographyAttribute()
    {
        return Str::normalizedNewLines(trim($this->bio));
    }
}
