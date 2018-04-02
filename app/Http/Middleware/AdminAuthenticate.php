<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuthenticate
{

    /**
     * Check admin permission base on route action and config/permissions.php
     * @param $action
     * @return bool
     */
    protected function missingPermission($action)
    {
        $user = auth('backend')->user();
        return ($user->permission_id && in_array($action, config('permissions')[$user->permission_id]['permission']));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //login check

        if (auth('backend')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('admin/login');
            }
        }

        //permission check.

        $action = last(explode('\\', request()->route()->getActionName()));

        if (!$this->missingPermission($action)) {
            flash('Admin permission required!', 'error');
            return redirect('/');
        }

        return $next($request);
    }
}
