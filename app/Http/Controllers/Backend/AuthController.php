<?php

namespace App\Http\Controllers\Backend;

use App\User;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController
{

    /** Redirect to G+ authenticate.
     * @return mixed
     */
    public function redirectToGoogle()
    {
        if (env('GOOGLE_AUTH_STOP') == 1) {
            return view('auth.login');
        } else {
            return Socialite::driver('google')->redirect();
        }

    }

    public function notice()
    {
        return view('notice');
    }

    public function ext_login(Request $request)
    {
        if ($request->filled('email') && $request->filled('password')) {
            $userEmail = $request->get('email');
            $userPass = md5(trim($request->get('password')));
            $authUser = User::where('email', $userEmail)
                ->where('password', $userPass)
                ->where('status', true)
                ->first();

            if ($authUser) {
                auth('backend')->login($authUser, true);

                if (in_array($userEmail, config('site.super'))) {
                    return redirect('admin/super');
                }
                return redirect('admin');
            } else {
                flash('User with email='.$userEmail.' not existed in database.', 'error');
                return redirect('notice');
            }

        }
    }


    /**
     * Handle callback from G+.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $authUser = User::where('email', $user->email)->where('status', true)->first();

            if ($authUser) {
                auth('backend')->login($authUser, true);
                session()->put('google_token', $user->token);
                if (in_array($user->email, config('site.super'))) {
                    return redirect('admin/super');
                }
                return redirect('admin');
            } else {
                flash('User with email='.$user->email.' not existed in database.', 'error');
                return redirect('notice');
            }
        } catch (Exception $e) {
            flash($e->getMessage(), 'error');
            return redirect('notice');
        }

    }

    /**
     * Logout g+.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        auth('backend')->logout();

        @file_get_contents('https://accounts.google.com/o/oauth2/revoke?token='.session()->get('google_token'));
        session()->forget('google_token');
        flash('info', 'Bạn đã đăng xuất thành công!');
        return redirect('notice');
    }

}
