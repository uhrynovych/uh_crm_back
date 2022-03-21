<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Media;

class Magazine extends Model {
    protected $fillable = [
        'name',
        'logo_id',
        'url',
        'key',
        'status'
    ];

    public function media() {
        return $this->hasOne(Media::class, 'id', 'logo_id');
    }
}
