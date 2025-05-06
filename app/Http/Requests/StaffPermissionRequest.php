<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffPermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'manage_orders' => 'sometimes|boolean',
            'manage_products' => 'sometimes|boolean',
            'manage_settings' => 'sometimes|boolean',
        ];
    }
}
