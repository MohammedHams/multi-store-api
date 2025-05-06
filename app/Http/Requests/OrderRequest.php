<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'store_id' => 'required|exists:stores,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'status' => 'sometimes|in:pending,completed,cancelled'
        ];
    }

    public function messages()
    {
        return [
            'store_id.required' => 'حقل المتجر مطلوب',
            'products.min' => 'يجب إضافة منتج واحد على الأقل',
            'phone' => 'required_if:send_whatsapp,true|phone:INTERNATIONAL'

        ];
    }
}
