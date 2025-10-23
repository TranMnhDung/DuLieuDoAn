<?php
// app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            
            // Nếu yêu cầu có prefix 'admin', chuyển hướng về admin.login
            if ($request->is('admin/*') || $request->is('admin')) { 
                 return route('admin.login');
            }
            
            // Chuyển hướng về route 'login' mặc định (nếu có)
            return route('login'); 
        }
    }
}