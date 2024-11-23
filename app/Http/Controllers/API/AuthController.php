<?php

namespace App\Http\Controllers\API;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\SiteTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use SiteTrait;

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request['email'])->first();

            if ($user?->status == UserStatus::INACTIVE->value) {
                return $this->sendResponse(401, 'Akun anda tidak aktif!');
            } elseif (auth()->attempt($credentials)) {
                $data = [
                    'user' => $user,
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ];

                return $this->sendResponse(200, 'Berhasil login!', $data);
            }

            return $this->sendResponse(401, 'Email atau password salah!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse(401, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}
