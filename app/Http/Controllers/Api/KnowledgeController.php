<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KnowledgeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KnowledgeController extends Controller
{
    protected $knowledgeService;

    public function __construct(KnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * 获取所有 Python 版本列表
     *
     * @return JsonResponse
     */
    public function getVersions(): JsonResponse
    {
        $versions = $this->knowledgeService->getVersions();
        return response()->json([
            'success' => true,
            'data' => $versions
        ]);
    }

    /**
     * 获取所有知识分类
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = $this->knowledgeService->getCategories();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * 获取所有标签
     *
     * @return JsonResponse
     */
    public function getTags(): JsonResponse
    {
        $tags = $this->knowledgeService->getTags();
        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }

    /**
     * 获取知识列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->only(['version', 'category', 'tag', 'keyword', 'page', 'per_page', 'sort', 'order']);
        $knowledge = $this->knowledgeService->getKnowledgeList($params);
        return response()->json([
            'success' => true,
            'data' => $knowledge
        ]);
    }

    /**
     * 获取知识详情
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $knowledge = $this->knowledgeService->getKnowledgeDetail($id);
        if (!$knowledge) {
            return response()->json([
                'success' => false,
                'message' => '知识不存在'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $knowledge
        ]);
    }

    /**
     * 获取指定版本的知识
     *
     * @param string $version
     * @return JsonResponse
     */
    public function getByVersion(string $version): JsonResponse
    {
        $knowledge = $this->knowledgeService->getKnowledgeByVersion($version);
        return response()->json([
            'success' => true,
            'data' => $knowledge
        ]);
    }

    /**
     * 获取指定分类的知识
     *
     * @param string $category
     * @return JsonResponse
     */
    public function getByCategory(string $category): JsonResponse
    {
        $knowledge = $this->knowledgeService->getKnowledgeByCategory($category);
        return response()->json([
            'success' => true,
            'data' => $knowledge
        ]);
    }

    /**
     * 搜索知识
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->input('keyword');
        $params = $request->only(['version', 'category', 'tag', 'page', 'per_page']);
        $results = $this->knowledgeService->search($keyword, $params);
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * 收藏知识
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function favorite(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $knowledgeBaseId = $request->input('knowledge_base_id');
        $result = $this->knowledgeService->favorite($user->id, $id, $knowledgeBaseId);
        return response()->json([
            'success' => true,
            'message' => '收藏成功',
            'data' => $result
        ]);
    }

    /**
     * 取消收藏知识
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function removeFavorite(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $this->knowledgeService->removeFavorite($user->id, $id);
        return response()->json([
            'success' => true,
            'message' => '已取消收藏'
        ]);
    }
}
