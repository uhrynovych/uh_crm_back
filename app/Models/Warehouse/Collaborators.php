<?php

namespace App\Models\Warehouse;
use App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class Collaborators extends Model {
    public $table = "collaborators_сontractor";

    protected $fillable = [
        'first_name',
        'last_name',
        'birthday',
        'address',
        'email',
        'phone',
        'viber',
        'telegram',
        'skype',
        'role',
        'description',
        'photo_id',
        'contractor_id',
        'status'
    ];

    public function media() {
        return $this->hasOne(Media::class, 'id', 'photo_id');
    }
}
