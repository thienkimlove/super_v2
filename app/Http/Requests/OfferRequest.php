<?php

namespace App\Http\Requests;

use App\Offer;
use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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

    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function ($validator) {
            // Call the after method of the FormRequest (see below)
            $this->after($validator);
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255',
            'redirect_link' => 'required|max:255',
            'click_rate' => 'required',
            'geo_locations' => 'required',
            'allow_devices' => 'required',
            'network_id' => 'required',
            'net_offer_id' => 'required',
        ];



        return $rules;
    }

    public function after($validator)
    {

    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không để trống tên offer',
            'redirect_link.required' => 'Vui lòng không để trống link',
            'click_rate.required' => 'Vui lòng chọn click rate',
            'geo_locations.required' => 'Vui lòng chọn geo locations',
            'allow_devices.required' => 'Vui lòng chọn allow devices',
            'network_id.required' => 'Vui lòng chọn network',
            'net_offer_id.required' => 'Vui lòng chọn net offer Id',
        ];
    }

    public function store()
    {
        if (!$this->filled('status')) {
            $this->merge([
                'status' => false,
            ]);
        }

        if (!$this->filled('auto')) {
            $this->merge([
                'auto' => false,
            ]);
        }

        if (!$this->filled('allow_multi_lead')) {
            $this->merge([
                'allow_multi_lead' => false,
            ]);
        }

        if (!$this->filled('check_click_in_network')) {
            $this->merge([
                'check_click_in_network' => false,
            ]);
        }

        if (!$this->filled('number_when_click')) {
            $this->merge([
                'number_when_click' => 0,
            ]);
        }

        if (!$this->filled('number_when_lead')) {
            $this->merge([
                'number_when_lead' => 0,
            ]);
        }


        Offer::create($this->all());


        return $this;
    }

    public function save($id)
    {
        $offer = Offer::findOrFail($id);

        if (!$this->filled('status')) {
            $this->merge([
                'status' => false,
            ]);
        }

        if (!$this->filled('auto')) {
            $this->merge([
                'auto' => false,
            ]);
        }

        if (!$this->filled('allow_multi_lead')) {
            $this->merge([
                'allow_multi_lead' => false,
            ]);
        }

        if (!$this->filled('check_click_in_network')) {
            $this->merge([
                'check_click_in_network' => false,
            ]);
        }

        if (!$this->filled('number_when_click')) {
            $this->merge([
                'number_when_click' => 0,
            ]);
        }

        if (!$this->filled('number_when_lead')) {
            $this->merge([
                'number_when_lead' => 0,
            ]);
        }


        $offer->update($this->all());


        return $this;
    }
}
