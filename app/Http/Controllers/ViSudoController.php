<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ViSudoController extends Controller
{
    public function loginAsUser(Request $request)
    {
        session()->put('wh.sudosu.has_sudoed', true);
        session()->put('wh.sudosu.original_id', $request->originalUserId);
        $user = User::find($request->userId);
        auth('backend')->login($user, true);

        return redirect()->back();
    }

    public function return(Request $request)
    {
        if (! session()->has('wh.sudosu.has_sudoed')) {
            return redirect()->back();
        }

        auth('backend')->logout();

        $originalUserId = session('wh.sudosu.original_id');
        if ($originalUserId) {
            $user = User::find($originalUserId);
            auth('backend')->login($user, true);
        }

        session()->forget('wh.sudosu.original_id');
        session()->forget('wh.sudosu.has_sudoed');

        return redirect()->back();
    }
}