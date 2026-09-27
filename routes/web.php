<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/knowledge', function () {
    return view('knowledge');
})->name('knowledge');

Route::get('/knowledge/{id}', function ($id) {
    return view('knowledge-detail', ['id' => $id]);
})->name('knowledge.detail');

Route::get('/knowledge-base', function () {
    return view('knowledge-base');
})->name('knowledge-base');

Route::get('/knowledge-base/{id}', function ($id) {
    return view('knowledge-base-detail', ['id' => $id]);
})->name('knowledge-base.detail');

Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::get('/share/{shareId}', function ($shareId) {
    return view('share', ['shareId' => $shareId]);
})->name('share');

Route::get('/admin', function () {
    return view('admin');
})->name('admin')->middleware('can:access-admin');

Route::get('/admin/users', function () {
    return view('admin.users');
})->name('admin.users')->middleware('can:manage-users');

Route::get('/admin/knowledge-bases', function () {
    return view('admin.knowledge-bases');
})->name('admin.knowledge-bases')->middleware('can:manage-knowledge-bases');

Route::get('/admin/settings', function () {
    return view('admin.settings');
})->name('admin.settings')->middleware('can:manage-settings');

Route::get('/admin/logs', function () {
    return view('admin.logs');
})->name('admin.logs')->middleware('can:manage-logs');
