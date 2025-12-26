<?php

use App\Http\Controllers\Admin\AppUserManagerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostManagerController;
use App\Http\Controllers\Admin\ReportManagerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/login', function () {
    return view('login.login');
})->name('login')->middleware('guest');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);

})->name('login.submit');




Route::middleware('auth')->group(function () {
    // 1. Dashboard (Real Stats)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // 2. Post Management
    Route::get('/admin/posts', [PostManagerController::class, 'index'])->name('admin.posts.index');
    Route::post('/admin/posts/{id}/ban', [PostManagerController::class, 'ban'])->name('admin.posts.ban');
    Route::delete('/admin/posts/{id}', [PostManagerController::class, 'destroy'])->name('admin.posts.delete');
    Route::get('/admin/posts/{id}/comments', [PostManagerController::class, 'comments'])->name('admin.posts.comments');

    Route::delete('/admin/comments/{id}', [PostManagerController::class, 'deleteComment'])
        ->name('admin.comments.delete');


    Route::get('/admin/users', [AppUserManagerController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{id}', [AppUserManagerController::class, 'show'])->name('admin.users.show'); // For Modal
    Route::post('/admin/users/{id}/ban', [AppUserManagerController::class, 'toggleBan'])->name('admin.users.ban');
    Route::delete('/admin/users/{id}', [AppUserManagerController::class, 'destroy'])->name('admin.users.delete');

    Route::get('/admin/reports', [ReportManagerController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/{id}', [ReportManagerController::class, 'show'])->name('admin.reports.show');
    Route::post('/admin/reports/{id}', [ReportManagerController::class, 'updateStatus'])->name('admin.reports.update');

});



Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/', function () {
    return redirect('/dashboard');
});