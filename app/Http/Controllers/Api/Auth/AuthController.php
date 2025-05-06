<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;

class AuthController extends Controller
{
    public function superAdminLogin(LoginRequest $request)
    {
        return $this->attemptLogin($request, User::SUPER_ADMIN);
    }

    public function storeOwnerLogin(LoginRequest $request)
    {
        return $this->attemptLogin($request, User::STORE_OWNER);
    }

    public function staffLogin(LoginRequest $request)
    {
        return $this->attemptLogin($request, User::STAFF);
    }


    public function attemptLogin(LoginRequest $request, $userType)
    {
        $user = User::where('email', $request['email'])
            ->where('type', $userType)
            ->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'المعلومات غير صحيحة'], 401);
        }

        $otp = rand(100000, 999999);
        OtpVerification::updateOrCreate(
            ['user_id' => $user->id],
            ['otp_code' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        Mail::to($user->email)->send(new SendOtp($otp));

        return response()->json([
            'message' => 'تم إرسال OTP إلى بريدك الإلكتروني',
            'user_id' => $user->id,
        ]);
    }
}
