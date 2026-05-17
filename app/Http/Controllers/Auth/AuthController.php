<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([

            'username' => '
                required|
                string
            ',

            'password' => '
                required|
                string
            ',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Check User Exists
        |--------------------------------------------------------------------------
        */

        $user = User::where('username', $request->username)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | User Not Found
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            return back()
                ->withErrors([
                    'username' => 'Invalid username or password.'
                ])
                ->onlyInput('username');

        }

        /*
        |--------------------------------------------------------------------------
        | Check User Status
        |--------------------------------------------------------------------------
        */

        if ($user->status != 1) {

            return back()
                ->withErrors([
                    'username' => 'Your account is not active yet.'
                ])
                ->onlyInput('username');

        }

        /*
        |--------------------------------------------------------------------------
        | Attempt Login
        |--------------------------------------------------------------------------
        */

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {

            /*
            |--------------------------------------------------------------------------
            | Regenerate Session
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();

            /*
            |--------------------------------------------------------------------------
            | Update Last Login
            |--------------------------------------------------------------------------
            */

            auth()->user()->update([
                'last_login_at' => now()
            ]);

            /*
            |--------------------------------------------------------------------------
            | Redirect Dashboard
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('dashboard.index');

        }

        /*
        |--------------------------------------------------------------------------
        | Failed Login
        |--------------------------------------------------------------------------
        */

        return back()
            ->withErrors([
                'username' => 'Invalid username or password.'
            ])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function changePasswordView()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'current_password' => '
                required|
                string
            ',

            'password' => '
                required|
                string|
                min:6|
                confirmed
            ',

        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Auth User
            |--------------------------------------------------------------------------
            */

            $user = auth()->user();

            /*
            |--------------------------------------------------------------------------
            | Check Current Password
            |--------------------------------------------------------------------------
            */

            if (
                !Hash::check(
                    $request->current_password,
                    $user->password
                )
            ) {

                return back()
                    ->withErrors([
                        'current_password' =>
                            'Current password is incorrect.'
                    ])
                    ->withInput();

            }

            /*
            |--------------------------------------------------------------------------
            | Prevent Same Password
            |--------------------------------------------------------------------------
            */

            if (
                Hash::check(
                    $request->password,
                    $user->password
                )
            ) {

                return back()
                    ->withErrors([
                        'password' =>
                            'New password cannot be the same as the old password.'
                    ])
                    ->withInput();

            }

            /*
            |--------------------------------------------------------------------------
            | Update Password
            |--------------------------------------------------------------------------
            */

            $user->update([

                'password' => Hash::make(
                    $request->password
                ),

            ]);

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success Redirect
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Password updated successfully.'
                );

        } catch (Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );

        }
    }
}
