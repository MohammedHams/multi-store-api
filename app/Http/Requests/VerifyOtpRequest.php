<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'المستخدم غير موجود.',
            'otp_code.numeric' => 'يجب أن يكون OTP رقميًا.',
        ];
    }
}
