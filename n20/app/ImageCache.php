<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageCache extends Model
{
    protected $table = 'image_caches';

    protected $fillable = [
        'image_id',
        'width',
        'height',
        'extension',
        'filename',
        'url',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getSizeAttribute()
    {
        $width = (int) $this->attributes['width'];
        $height = (int) $this->attributes['height'];

        return $width * $height;
    }

    public function originalImage()
    {
        return $this->belongsTo('App\Image', 'image_id', 'image_id');
    }
}
