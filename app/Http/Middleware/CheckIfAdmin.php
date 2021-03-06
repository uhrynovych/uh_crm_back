<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAdmin {

    public function handle($request, Closure $next) {
        $user = $request->user();

        if (!isset($user)) {
            return redirect('admin/login');
        }

        if (!$user->isAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
