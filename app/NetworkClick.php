<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NetworkClick extends Model
{
    protected $fillable = [
        'network_id',
        'network_offer_id',
        'sub_id',
        'amount',
        'ip',
        'offer_id',
        'click_id',
        'status',
        'json_data',
    ];

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
