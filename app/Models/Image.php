<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'photo';

    public function product()
    {
        return $this->belongsTo(Product::class, 'obj_id');
    }

    /**
     * cleanPath
     */
    public function getCleanPathAttribute()
    {
        $path = preg_replace('/^(.*?\\\\BD\\\\)(.+)$/i', '$2', $this->pathphotobd);
        $path = str_replace('\\', '/', $path);
        return $path . '/' . $this->nomphoto;
    }
}
