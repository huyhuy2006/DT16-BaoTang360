<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create(['name'=>$request->string('name'),'email'=>$request->string('email'),'password'=>$request->string('password'),'role'=>'visitor']);
        return response()->json(['user'=>$user,'token'=>$user->createToken('spa-token')->plainTextToken],201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->string('email'))->first();
        if (!$user || !Hash::check($request->string('password'), $user->password) || ! $user->is_active) return response()->json(['message'=>'Email hoặc mật khẩu không đúng.'],401);
        $user->tokens()->where('name','spa-token')->delete();
        return response()->json(['user'=>$user,'token'=>$user->createToken('spa-token')->plainTextToken]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) $token->delete();
        return response()->json(['message'=>'Đã đăng xuất.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateMe(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->fill($data)->save();

        return response()->json($user->fresh());
    }
}
