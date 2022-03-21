<?php

namespace App\Models\Warehouse;
use App\Models\Warehouse\Collaborators;
use App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class Сontractor extends Model {
    public $table = "contractors";

    protected $fillable = [
        'name',
        'description',
        'address',
        'website',
        'logo_id',
        'status'
    ];

    public function media() {
        return $this->hasOne(Media::class, 'id', 'logo_id');
    }

    public function collaborators() {
        return $this->hasMany(Collaborators::class, 'contractor_id', 'id');
    }
}
