<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'redirect_link',
        'click_rate',
        'geo_locations',
        'allow_devices',
        'network_id',
        'net_offer_id',
        'image',
        'status',
        'auto',
        'allow_multi_lead',
        'check_click_in_network',
        'number_when_click',
        'number_when_lead',
        'test_link'
    ];

    public $dates = ['created_at', 'updated_at'];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function leads()
    {
        return $this->hasMany(NetworkClick::class);
    }

}
