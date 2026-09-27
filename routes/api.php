<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KnowledgeController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\Api\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | 认证相关路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
        Route::post('/password/reset', [AuthController::class, 'resetPassword']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail']);
        Route::get('/github', [AuthController::class, 'redirectToGithub']);
        Route::get('/github/callback', [AuthController::class, 'handleGithubCallback']);
        Route::post('/github/link', [AuthController::class, 'linkGithubAccount'])->middleware('auth:sanctum');
        Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
        Route::put('/user/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
        Route::put('/user/password', [AuthController::class, 'updatePassword'])->middleware('auth:sanctum');
        Route::delete('/user', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');
    });

    /*
    |--------------------------------------------------------------------------
    | 知识查询路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('knowledge')->group(function () {
        Route::get('/versions', [KnowledgeController::class, 'getVersions']);
        Route::get('/categories', [KnowledgeController::class, 'getCategories']);
        Route::get('/tags', [KnowledgeController::class, 'getTags']);
        Route::get('/', [KnowledgeController::class, 'index']);
        Route::get('/{id}', [KnowledgeController::class, 'show']);
        Route::get('/version/{version}', [KnowledgeController::class, 'getByVersion']);
        Route::get('/category/{category}', [KnowledgeController::class, 'getByCategory']);
        Route::post('/search', [KnowledgeController::class, 'search']);
        Route::post('/{id}/favorite', [KnowledgeController::class, 'favorite'])->middleware('auth:sanctum');
        Route::delete('/{id}/favorite', [KnowledgeController::class, 'removeFavorite'])->middleware('auth:sanctum');
    });

    /*
    |--------------------------------------------------------------------------
    | 搜索路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('search')->group(function () {
        Route::get('/', [SearchController::class, 'index']);
        Route::get('/suggestions', [SearchController::class, 'getSuggestions']);
        Route::get('/history', [SearchController::class, 'getHistory']);
        Route::delete('/history', [SearchController::class, 'clearHistory']);
    });

    /*
    |--------------------------------------------------------------------------
    | 个人知识库路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('knowledge-base')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index']);
        Route::post('/', [KnowledgeBaseController::class, 'store']);
        Route::get('/{id}', [KnowledgeBaseController::class, 'show']);
        Route::put('/{id}', [KnowledgeBaseController::class, 'update']);
        Route::delete('/{id}', [KnowledgeBaseController::class, 'destroy']);
        Route::post('/{id}/entries', [KnowledgeBaseController::class, 'addEntry']);
        Route::put('/{id}/entries/{entryId}', [KnowledgeBaseController::class, 'updateEntry']);
        Route::delete('/{id}/entries/{entryId}', [KnowledgeBaseController::class, 'removeEntry']);
        Route::post('/{id}/categories', [KnowledgeBaseController::class, 'addCategory']);
        Route::delete('/{id}/categories/{categoryId}', [KnowledgeBaseController::class, 'removeCategory']);
        Route::get('/{id}/export', [KnowledgeBaseController::class, 'export']);
        Route::post('/{id}/share', [ShareController::class, 'create']);
        Route::delete('/{id}/share', [ShareController::class, 'cancel']);
    });

    /*
    |--------------------------------------------------------------------------
    | 分享路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('share')->group(function () {
        Route::get('/{shareId}', [ShareController::class, 'show']);
        Route::get('/{shareId}/entries', [ShareController::class, 'getEntries']);
        Route::get('/{shareId}/entries/{entryId}', [ShareController::class, 'getEntry']);
        Route::post('/{shareId}/favorite', [ShareController::class, 'favorite']);
        Route::get('/{shareId}/stats', [ShareController::class, 'getStats']);
    });

    /*
    |--------------------------------------------------------------------------
    | 管理员路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware(['auth:sanctum', 'role:system-admin,content-admin'])->group(function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/users/{id}/disable', [AdminController::class, 'disableUser']);
        Route::post('/users/{id}/enable', [AdminController::class, 'enableUser']);
        Route::get('/knowledge-bases', [AdminController::class, 'getKnowledgeBases']);
        Route::delete('/knowledge-bases/{id}', [AdminController::class, 'deleteKnowledgeBase']);
        Route::post('/knowledge-bases/{id}/approve', [AdminController::class, 'approveKnowledgeBase']);
        Route::post('/knowledge-bases/{id}/reject', [AdminController::class, 'rejectKnowledgeBase']);
        Route::get('/reports', [AdminController::class, 'getReports']);
        Route::get('/notifications', [AdminController::class, 'getNotifications']);
        Route::post('/notifications', [AdminController::class, 'sendNotification']);
        Route::delete('/notifications/{id}', [AdminController::class, 'deleteNotification']);
        Route::get('/settings', [AdminController::class, 'getSettings']);
        Route::put('/settings', [AdminController::class, 'updateSettings']);
        Route::get('/logs', [AdminController::class, 'getLogs']);
        Route::get('/stats', [AdminController::class, 'getStats']);
        Route::post('/backup', [AdminController::class, 'createBackup']);
        Route::get('/backups', [AdminController::class, 'getBackups']);
        Route::post('/backups/{id}/restore', [AdminController::class, 'restoreBackup']);
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
    });

    /*
    |--------------------------------------------------------------------------
    | 系统路由
    |--------------------------------------------------------------------------
    */
    Route::prefix('system')->middleware(['auth:sanctum', 'role:system-admin'])->group(function () {
        Route::get('/health', [AdminController::class, 'getHealthStatus']);
        Route::get('/config', [AdminController::class, 'getConfig']);
        Route::put('/config', [AdminController::class, 'updateConfig']);
        Route::get('/cache', [AdminController::class, 'getCacheStatus']);
        Route::post('/cache/clear', [AdminController::class, 'clearCache']);
        Route::get('/queue', [AdminController::class, 'getQueueStatus']);
        Route::get('/database', [AdminController::class, 'getDatabaseStatus']);
    });
});
