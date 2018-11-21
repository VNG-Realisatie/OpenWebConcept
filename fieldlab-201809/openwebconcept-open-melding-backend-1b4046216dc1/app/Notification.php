<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $table = 'notifications';

    protected $fillable = [ 'message', 'lat', 'lng', 'phone', 'email', 'time' ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality');
    }

}