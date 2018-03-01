<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
        $validations = [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];

        foreach ($this->request->get('attributes', []) as $attributeId => $value) {
            $validations["attributes.{$attributeId}"] = 'required';
        }
        return $validations;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $request = $this->request;
        if ($request->has('attributes')) {
            $validator->after(function ($validator) use ($request) {
                $attributeIds = array_keys($request->get('attributes'));

                if (count($attributeIds) !== ProductAttribute::whereIn('attribute_id', $attributeIds)
                        ->where('product_id', $request->get('product_id'))
                        ->count()
                ) {
                    $validator->errors()->add('attributes', 'Invalid parameters.');
                }
            });
        }

    }

}
