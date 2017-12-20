<?php

namespace App\Http\Controllers\Backend;

use App\User;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class AuthController
{

    /** Redirect to G+ authenticate.
     * @return mixed
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    /**
     * Handle callback from G+.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $authUser = User::where('email', $user->email)->first();

            if ($authUser) {
                auth('backend')->login($authUser, true);
                return redirect('admin');
            } else {
                flash('User with email='.$user->email.' not existed in database.', 'error');
                return redirect('/');
            }
        } catch (Exception $e) {
            return redirect('admin/login');
        }

    }

    /**
     * Logout g+.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        auth('backend')->logout();

        return redirect('/');
    }

}
