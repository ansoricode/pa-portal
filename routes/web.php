<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ComplianceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\TwoFactorChallengeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\AnnouncementController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\SurveyController;
use App\Http\Middleware\CountVisit;
use App\Http\Middleware\EnsureSecurity;
use App\Http\Middleware\RequirePermission;
use App\Http\Middleware\RestrictAdminIp;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Support\Facades\Route;

$loginPath = trim(config('portal.admin_login_path'), '/');
$adminPath = trim(config('portal.admin_path'), '/');

Route::middleware(SecurityHeaders::class)->group(function () use ($loginPath, $adminPath) {

    /*
    |--------------------------------------------------------------------------
    | Login & panel admin (URL diatur lewat ADMIN_LOGIN_PATH dan ADMIN_PATH di .env)
    |--------------------------------------------------------------------------
    */
    Route::middleware(RestrictAdminIp::class)->group(function () use ($loginPath, $adminPath) {

        Route::get($loginPath, [AuthController::class, 'showLogin'])->name('login');
        Route::post($loginPath, [AuthController::class, 'login'])->middleware('throttle:20,1')->name('admin.login.submit');
        Route::get($loginPath.'/verifikasi', [TwoFactorChallengeController::class, 'show'])->name('admin.2fa.challenge');
        Route::post($loginPath.'/verifikasi', [TwoFactorChallengeController::class, 'verify'])->middleware('throttle:20,1')->name('admin.2fa.verify');

        Route::prefix($adminPath)->middleware(['auth', EnsureSecurity::class])->name('admin.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');

            // Akun & keamanan pribadi
            Route::get('akun', [AuthController::class, 'account'])->name('account');
            Route::put('akun', [AuthController::class, 'updateAccount'])->name('account.update');
            Route::get('keamanan', [SecurityController::class, 'show'])->name('security');
            Route::post('keamanan/2fa/aktifkan', [SecurityController::class, 'enable'])->name('security.enable');
            Route::post('keamanan/2fa/konfirmasi', [SecurityController::class, 'confirm'])->middleware('throttle:10,1')->name('security.confirm');
            Route::post('keamanan/2fa/kode-pemulihan', [SecurityController::class, 'regenerate'])->middleware('throttle:10,1')->name('security.regenerate');
            Route::post('keamanan/2fa/nonaktifkan', [SecurityController::class, 'disable'])->middleware('throttle:10,1')->name('security.disable');

            // Khusus Super Admin
            Route::middleware(RequirePermission::class.':users')->group(function () {
                Route::get('pengguna', [UserController::class, 'index'])->name('users.index');
                Route::get('pengguna/tambah', [UserController::class, 'create'])->name('users.create');
                Route::post('pengguna', [UserController::class, 'store'])->name('users.store');
                Route::get('pengguna/{user}/ubah', [UserController::class, 'edit'])->whereNumber('user')->name('users.edit');
                Route::put('pengguna/{user}', [UserController::class, 'update'])->whereNumber('user')->name('users.update');
                Route::delete('pengguna/{user}', [UserController::class, 'destroy'])->whereNumber('user')->name('users.destroy');
                Route::post('pengguna/{user}/reset-2fa', [UserController::class, 'reset2fa'])->whereNumber('user')->name('users.reset2fa');
            });
            Route::get('log-aktivitas', [LogController::class, 'index'])->middleware(RequirePermission::class.':logs')->name('logs');

            // Pengaturan situs
            Route::middleware(RequirePermission::class.':settings')->group(function () {
                Route::get('pengaturan', [SettingController::class, 'edit'])->name('settings');
                Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');
                Route::get('pengaturan-footer', [FooterController::class, 'edit'])->name('footer.edit');
                Route::put('pengaturan-footer', [FooterController::class, 'update'])->name('footer.update');
            });

            Route::get('monitoring-kepatuhan', [ComplianceController::class, 'index'])->name('compliance.index');
            Route::put('monitoring-kepatuhan/{code}', [ComplianceController::class, 'update'])
                ->where('code', '[a-z0-9_]+')
                ->name('compliance.update');
            Route::post('monitoring-kepatuhan/{code}/check-link', [ComplianceController::class, 'checkLink'])
                ->where('code', '[a-z0-9_]+')
                ->name('compliance.check-link');

            // CRUD generik (config/admin_resources.php) — izin dicek per peran di ResourceController
            Route::get('{resource}', [ResourceController::class, 'index'])->name('resource.index');
            Route::get('{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
            Route::post('{resource}', [ResourceController::class, 'store'])->name('resource.store');
            Route::get('{resource}/{id}/edit', [ResourceController::class, 'edit'])->whereNumber('id')->name('resource.edit');
            Route::put('{resource}/{id}', [ResourceController::class, 'update'])->whereNumber('id')->name('resource.update');
            Route::delete('{resource}/{id}', [ResourceController::class, 'destroy'])->whereNumber('id')->name('resource.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Portal publik (frontend)
    |--------------------------------------------------------------------------
    */
    Route::middleware(CountVisit::class)->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
        Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

        Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/pengumuman/{announcement}/kunjungi', [AnnouncementController::class, 'visit'])
            ->whereNumber('announcement')
            ->name('announcements.visit');

        Route::get('/cari', [SearchController::class, 'index'])->name('search');

        Route::get('/{section}/{slug?}', [PageController::class, 'show'])
            ->where('section', 'profil|layanan-hukum|transparansi|layanan-publik')
            ->name('page.show');
    });

    Route::post('/survei', [SurveyController::class, 'store'])->middleware('throttle:5,1')->name('survey.store');
});
