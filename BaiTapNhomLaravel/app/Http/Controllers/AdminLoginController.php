<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NhanVien;

class AdminLoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập Admin 
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login'); 
    }

    /**
     * Xử lý logic đăng nhập Admin
     */
    public function login(Request $request)
    {
        // 1. Validation 
        $request->validate([
            'Ten' => 'required|string', 
            'Pass' => 'required|string', 
        ], [
            'Ten.required' => 'Vui lòng nhập tên đăng nhập.',
            'Pass.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        
        
        // 1. Tìm tài khoản bằng tên (sử dụng Model đã sửa tên bảng)
        
        $nhanVien = NhanVien::where('Ten', $request->Ten)->first();

        // 2. Kiểm tra tài khoản có tồn tại không
        if (!$nhanVien) {
             return back()->withInput()->withErrors(['Ten' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
        }
        
        // 3. Kiểm tra mật khẩu
        if ($request->Pass === $nhanVien->Pass) {
            
            // 4. Kiểm tra Vai Trò và Trạng Thái
            if ($nhanVien->VaiTro === 'admin' && $nhanVien->TrangThai == 1) { 
                
                // Đăng nhập thành công: Ghi nhớ session thủ công
                Auth::guard('admin')->login($nhanVien, $request->filled('remember')); 

                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // Tài khoản tồn tại, mật khẩu đúng, nhưng không đủ quyền/bị khóa
            return back()->withInput()->withErrors(['Ten' => 'Tài khoản không có quyền truy cập hoặc đang bị khóa.']);
        }

        // Mật khẩu không khớp
        return back()->withInput()->withErrors(['Ten' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
    }
    
    // /**
    //  * Xử lý Đăng xuất Admin
    //  */
    // public function logout(Request $request)
    // {
    //     Auth::guard('admin')->logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect()->route('admin.login');
    // }
}