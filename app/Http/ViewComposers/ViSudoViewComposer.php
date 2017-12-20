<?php

namespace App\Http\ViewComposers;

use App\User;
use Illuminate\View\View;

class ViSudoViewComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('allUsers', $this->getUsers());
        $view->with('hasSudoed', $this->hasSudoed());
        $view->with('originalUser', $this->getOriginalUser());
        $view->with('currentUser', $this->getCurrentUser());
    }

    protected function getCurrentUser()
    {
        return auth('backend')->user();
    }

    protected function getOriginalUser()
    {
        if (! $this->hasSudoed()) {
            return auth('backend')->user();
        }

        $userId = session('wh.sudosu.original_id');

        return User::find($userId);
    }

    protected function hasSudoed()
    {
        return session()->has('wh.sudosu.has_sudoed');
    }

    protected function getUsers()
    {
        return User::orderBy('created_at', 'asc')->pluck('username', 'id')->all();
    }
}