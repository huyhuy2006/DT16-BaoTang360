<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email'=>['required','email']]);
        Password::sendResetLink($request->only('email'));
        return response()->json(['message'=>'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu đã được gửi.']);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset($request->only('email','password','password_confirmation','token'), function($user,$password){$user->forceFill(['password'=>$password])->save();});
        if ($status !== Password::PASSWORD_RESET) return response()->json(['message'=>'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'],422);
        return response()->json(['message'=>'Đặt lại mật khẩu thành công. Hãy đăng nhập lại.']);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user=$request->user();
        if (!Hash::check($request->string('current_password'),$user->password)) return response()->json(['message'=>'Mật khẩu hiện tại không đúng.'],422);
        $user->forceFill(['password'=>$request->string('password')])->save();
        $user->tokens()->delete();
        return response()->json(['message'=>'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.']);
    }
}
