<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordReset;
use App\Notifications\ResetPasswordCustommer;

class ResetPasswordCustomerController extends Controller
{
    public function forgotPassword(Request $request)
    {

        $customer = Customer::where('email', $request->email)->firstOrFail();
        $passwordReset = PasswordReset::updateOrCreate([
            'email' => $customer->email,
        ], [
            'token' => Str::random(60),
        ]);
        if ($passwordReset) {
            $customer->notify(new ResetPasswordCustommer($passwordReset->token));
        }

        return response()->json([
            'message' => 'Link Liên kết password reset đã được gửi!'
        ]);
    }
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:40',
            'passwordAgain' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        $token = $request->token;
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.',
            ]);
        }
        $customer = Customer::where('email', $passwordReset->email)->firstOrFail();
        $updatePasswordCustomer = $customer->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();
        $passwordReset->delete();

        return response()->json([
            'success' => $updatePasswordCustomer,
        ]);
    }
}
