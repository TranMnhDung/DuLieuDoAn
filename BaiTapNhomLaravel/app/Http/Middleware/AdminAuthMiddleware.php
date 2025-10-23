<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Thêm dòng này
use Illuminate\Support\Facades\Auth; 

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem đã đăng nhập với 'admin' guard chưa
        if (!Auth::guard('admin')->check()) {
            return redirect('/a/login')->with('error', 'Vui lòng đăng nhập để truy cập trang quản trị.');
        }

        // Kiểm tra quyền (VaiTro = Admin và TrangThai = 1)
        if (!Auth::guard('admin')->user()->isAdmin()) {
            Auth::guard('admin')->logout();
            return redirect('/a/login')->with('error', 'Bạn không có quyền truy cập vào khu vực này.');
        }

       return $next($request);
    }
}