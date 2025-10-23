<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginForm()
    {
        // Kiểm tra nếu đã đăng nhập rồi thì chuyển hướng về trang dashboard
        if (Auth::guard('admin')->check()) { 
            // Đảm bảo route 'a.dashboard' được định nghĩa trong routes/web.php
            return redirect()->route('a.dashboard'); 
        }
        // Trả về view('login') - Đảm bảo file login.blade.php nằm ở resources/views/
        return view('login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            // Laravel sẽ sử dụng 'email' cho tên lỗi và old input
            'email' => 'required', 
            'password' => 'required', 
        ]);   
        // Tùy chỉnh trường đăng nhập: 'Ten' và 'Pass' của bảng NhanVien
        $login_field = ['Ten' => $request->email, 'Pass' => $request->password]; 
        
        if (Auth::guard('admin')->attempt($login_field, $request->filled('remember'))) {
            
            // Kiểm tra VaiTro = Admin và TrangThai = 1 (Giả sử logic isAdmin() có trong Model NhanVien)
            if(Auth::guard('admin')->user()->isAdmin()) {
                $request->session()->regenerate();
                return redirect()->intended(route('a.dashboard')) 
                                 ->with('success', 'Đăng nhập thành công!');
            }
        }
        // Quay lại form với lỗi xác thực
        return back()->withErrors([
            'email' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Chuyển hướng về trang đăng nhập /a/login
        return redirect('/a/login')->with('success', 'Bạn đã đăng xuất.');
    }
}