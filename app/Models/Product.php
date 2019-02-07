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

    /***
     * SCOM idiosyncrasy: defined as n-t-n relation in DB, but
     * displayed as single text field in UI.
     * See getLegacyInventoryNumberAttribute() below.
     */
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

    public function upholstery()
    {
        return $this->belongsToMany(Upholstery::class, 'objgar', 'obj_id', 'codgar');
    }
    
    public function publicationState()
    {
        return $this->belongsTo(PublicationState::class, 'numdiffusion')->withDefault([
            'coddiffusion' => 'NP-null',
        ]);
    }



    // Scopes

    public function scopeByInventory($query, $inventory)
    {
        return $query->where('obj.id', '=', $inventory);
    }


    // Accessors and mutators

    public function getConceptionYearAttribute()
    {
        return ($this->anncon === -1) ? null : $this->anncon;
    }

    public function getDescriptionAttribute()
    {
        return Str::normalizedNewLines(trim($this->des));
    }
    
    public function getBibliographyAttribute()
    {
        return Str::normalizedNewLines(trim($this->bio));
    }

    public function getIsPublishedAttribute()
    {
        return Str::normalizedNewLines(trim($this->bio));
    }

    /**
     * SCOM handles legacy inventory numbers as a n-to-n
     * relation, when in the UI it is displayed as a single
     * text field.
     *
     * @return string|null
     */
    public function getLegacyInventoryNumberAttribute()
    {
        $leginv = $this->legacyInventoryNumbers->first();
        if ($leginv) {
            return Str::normalizedNewLines(trim($leginv->ancnuminv));
        }
        return null;
    }
}
