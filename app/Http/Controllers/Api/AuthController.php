<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Verified;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * 用户注册
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nickname' => 'nullable|string|max:20',
            'name' => 'nullable|string|max:50'
        ]);

        $user = $this->authService->register(
            $validated['email'],
            $validated['password'],
            $validated['nickname'] ?? null,
            $validated['name'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => '注册成功，请验证邮箱',
            'data' => [
                'user' => $user,
                'requires_verification' => true
            ]
        ], 201);
    }

    /**
     * 用户登录
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $result = $this->authService->login(
            $validated['email'],
            $validated['password'],
            $validated['remember'] ?? false
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => '登录成功',
            'data' => $result['data']
        ]);
    }

    /**
     * 用户登出
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authService->logout($user);
        return response()->json([
            'success' => true,
            'message' => '登出成功'
        ]);
    }

    /**
     * 刷新 Token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $this->authService->refreshToken($user);
        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('sanctum.expiration')
            ]
        ]);
    }

    /**
     * 发送密码重置链接
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email'
        ]);

        $result = $this->authService->sendPasswordResetLink($validated['email']);
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }

    /**
     * 重置密码
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $result = $this->authService->resetPassword(
            $validated['token'],
            $validated['email'],
            $validated['password']
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }

    /**
     * 验证邮箱
     *
     * @param int $id
     * @param string $hash
     * @return JsonResponse
     */
    public function verifyEmail(int $id, string $hash): JsonResponse
    {
        $result = $this->authService->verifyEmail($id, $hash);
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        return response()->json([
            'success' => true,
            'message' => '邮箱验证成功'
        ]);
    }

    /**
     * 重定向到 GitHub 授权页面
     *
     * @return JsonResponse
     */
    public function redirectToGithub(): JsonResponse
    {
        $url = $this->authService->getGithubRedirectUrl();
        return response()->json([
            'success' => true,
            'data' => [
                'url' => $url
            ]
        ]);
    }

    /**
     * 处理 GitHub 回调
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleGithubCallback(Request $request): JsonResponse
    {
        $code = $request->input('code');
        $result = $this->authService->handleGithubCallback($code);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'GitHub 登录成功',
            'data' => $result['data']
        ]);
    }

    /**
     * 关联 GitHub 账号
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function linkGithubAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        $code = $request->input('code');
        $result = $this->authService->linkGithubAccount($user, $code);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'GitHub 账号关联成功',
            'data' => $result['data']
        ]);
    }

    /**
     * 获取当前用户信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * 更新个人资料
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'nickname' => 'sometimes|string|max:20',
            'avatar' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:200',
            'name' => 'nullable|string|max:50'
        ]);

        $updatedUser = $this->authService->updateProfile($user, $validated);
        return response()->json([
            'success' => true,
            'message' => '个人资料更新成功',
            'data' => $updatedUser
        ]);
    }

    /**
     * 更新密码
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string'
        ]);

        $result = $this->authService->updatePassword(
            $user,
            $validated['current_password'],
            $validated['new_password']
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => '密码更新成功'
        ]);
    }

    /**
     * 注销账号
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        $password = $request->validate(['password' => 'required|string']);
        $result = $this->authService->deleteAccount($user, $password['password']);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => '账号注销成功'
        ]);
    }
}
