<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'store_id' => 'required|exists:stores,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المنتج مطلوب.',
            'price.numeric' => 'السعر يجب أن يكون رقمًا.',
            'store_id.exists' => 'المتجر غير موجود.',
        ];
    }
}
