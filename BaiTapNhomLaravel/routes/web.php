<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\BicyclesController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SingleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController; // Thêm ProductController nếu cần
use Illuminate\Support\Facades\Route;

// Import Controllers Admin
use App\Http\Controllers\Admin\LoginController; 
use App\Http\Controllers\Admin\SanPhamController; 

/*
|--------------------------------------------------------------------------
| ROUTES DÀNH CHO NGƯỜI DÙNG (FRONT-END)
|--------------------------------------------------------------------------
*/
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('sanpham', [BicyclesController::class, 'bicycles'])->name('bicycles.index');
Route::get('giohang', [CartController::class, 'cart'])->name('cart.index');
Route::get('chitietsanpham', [SingleController::class, 'index'])->name('product.details');
Route::get('/contact', function () {
    return view('contact'); 
})->name('contact.show');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/contact/finalize', [ContactController::class, 'finalize'])->name('contact.finalize');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');


/*
|--------------------------------------------------------------------------
| ROUTES DÀNH CHO QUẢN TRỊ (ADMIN - PREFIX /a)
|--------------------------------------------------------------------------
*/

// 1. ROUTES CÔNG KHAI (LOGIN/LOGOUT): Không cần middleware 'admin'
Route::prefix('a')->group(function () {
    // Đường dẫn truy cập: /a/login
    Route::get('/login', [LoginController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login']);
    // Cần 1 form hoặc JS để gọi route này bằng POST
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout'); 
});


// 2. ROUTES ĐƯỢC BẢO VỆ (Dashboard, Sản Phẩm,...)
// Tên route: a.tenroute
Route::middleware(['admin'])->prefix('a')->name('a.')->group(function () {
    
    // ROUTE DASHBOARD: /a
    // Đây là route mà LoginController CHUYỂN HƯỚNG ĐẾN (tên: a.dashboard)
    Route::get('/', function () {
        return view('admin.dashboard'); 
    })->name('dashboard'); // Tên route: a.dashboard

    
    // ROUTES CHO QUẢN LÝ SẢN PHẨM
    // Xóa các định nghĩa route SanPham tùy chỉnh. Dùng Route::resource là đủ.
    Route::resource('sanpham', SanPhamController::class); // Tạo các routes: a.sanpham.index, a.sanpham.create, ...
    
});


// XÓA ROUTE XUNG ĐỘT (Đã bị route bảo vệ bên trên thay thế)
// Route::get('/a', function (): View { return view('admin.dashboard'); });