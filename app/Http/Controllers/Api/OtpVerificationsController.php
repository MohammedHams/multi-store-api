<?php

namespace App\Http\Controllers\Api;

use App\Models\OtpVerification;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;
use Illuminate\Http\JsonResponse;

class OtpVerificationsController extends Controller
{

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|numeric'
        ]);

        $otp = OtpVerification::where('user_id', $request->user_id)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'رمز OTP غير صالح أو منتهي الصلاحية'], 401);
        }

        $user = User::find($request->user_id);
        $token = $user->createToken('auth_token')->plainTextToken;

        $otp->delete();

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->user_id);
        $otp = rand(100000, 999999);

        OtpVerification::updateOrCreate(
            ['user_id' => $user->id],
            ['otp_code' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        Mail::to($user->email)->queue(new SendOtp($otp));

        return response()->json([
            'message' => 'تم إعادة إرسال OTP',
            'user_id' => $user->id
        ]);
    }
}
