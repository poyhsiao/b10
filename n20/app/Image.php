<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'image_id',
        'storage',
        'image_name',
        'ident',
        'url',
        'l_ident',
        'l_url',
        'm_ident',
        'm_url',
        's_ident',
        's_url',
        'width',
        'height',
        'watermark',
        'last_modified',
    ];

    protected $dates = [
        'last_modified',
        'created_at',
        'updated_at',
    ];

    protected $primaryKey = 'image_id';

    public function setImageIdAttribute()
    {
        $id = uniqid();

        return md5($id);
    }

    public function getSizeAttribue()
    {
        $width = $this->attributes['width'];
        $height = $this->attributes['height'];

        return ($width * $height);
    }
}
