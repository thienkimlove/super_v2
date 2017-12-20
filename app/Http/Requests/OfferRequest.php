<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class OfferRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'click_rate' => 'required',
            'redirect_link' => 'required',
            'geo_locations' => 'required',
            'allow_devices' => 'required',
            'network_id' => 'required',
            'net_offer_id' => 'required',
        ];
    }
}
