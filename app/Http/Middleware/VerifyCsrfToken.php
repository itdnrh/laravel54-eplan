<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    public function handle($request, Closure $next)
    {
        if($request->input('_token')) {
            if($request->input('_token') !== csrf_token()) {
                return redirect('/login')->with('status', 'Token expired!!');
            }
        }

        return parent::handle($request, $next);
    }
}
