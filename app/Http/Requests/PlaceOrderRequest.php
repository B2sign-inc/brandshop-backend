<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
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
        $validation['shipping.first_name'] = 'required|string|max:255';
        $validation['shipping.last_name'] = 'required|string|max:255';
        $validation['shipping.phone_number'] = 'required|integer';
        $validation['shipping.street_address'] = 'required|string|max:255';
        $validation['shipping.city'] = 'required|string|max:255';
        $validation['shipping.state'] = 'required|string|max:255';
        $validation['shipping.postcode'] = 'required|string|max:255';

        if ($this->request->get('use_different_billing_address')) {
            $validation['billing.first_name'] = 'required|string|max:255';
            $validation['billing.last_name'] = 'required|string|max:255';
            $validation['billing.phone_number'] = 'required|integer';
            $validation['billing.street_address'] = 'required|string|max:255';
            $validation['billing.city'] = 'required|string|max:255';
            $validation['billing.state'] = 'required|string|max:255';
            $validation['billing.postcode'] = 'required|string|max:255';
        }

        $validation['shipping_method_id'] = 'required|exists:shipping_methods,id';


        return $validation;
    }
}
