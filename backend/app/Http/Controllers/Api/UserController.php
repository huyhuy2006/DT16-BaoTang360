<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::query()
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->orderBy('name')
            ->get();
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Không thể tự đổi vai trò của chính mình.'], 422);
        }

        $user->update($request->validated());
        return response()->json($user->only(['id', 'name', 'email', 'role', 'is_active', 'created_at']));
    }

    public function updateStatus(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Không thể tự khóa tài khoản đang sử dụng.'], 422);
        }

        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $user->is_active = $data['is_active'];
        $user->save();

        if (! $user->is_active) {
            $user->tokens()->delete();
        }

        return response()->json($user->only(['id', 'name', 'email', 'role', 'is_active', 'created_at']));
    }
}
