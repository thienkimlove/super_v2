<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = ['name', 'type', 'api_url', 'cron'];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
