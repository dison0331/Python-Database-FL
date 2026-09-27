<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KnowledgeBaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KnowledgeBaseController extends Controller
{
    protected $knowledgeBaseService;

    public function __construct(KnowledgeBaseService $knowledgeBaseService)
    {
        $this->knowledgeBaseService = $knowledgeBaseService;
    }

    /**
     * 获取个人知识库列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $knowledgeBases = $this->knowledgeBaseService->getUserKnowledgeBases($user->id);
        return response()->json([
            'success' => true,
            'data' => $knowledgeBases
        ]);
    }

    /**
     * 创建知识库
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|array|max:10',
            'is_public' => 'boolean'
        ]);

        $knowledgeBase = $this->knowledgeBaseService->createKnowledgeBase(
            $user->id,
            $validated['name'],
            $validated['description'] ?? '',
            $validated['tags'] ?? [],
            $validated['is_public'] ?? false
        );

        return response()->json([
            'success' => true,
            'message' => '知识库创建成功',
            'data' => $knowledgeBase
        ], 201);
    }

    /**
     * 获取知识库详情
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $knowledgeBase = $this->knowledgeBaseService->getKnowledgeBase($id);
        if (!$knowledgeBase) {
            return response()->json([
                'success' => false,
                'message' => '知识库不存在'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $knowledgeBase
        ]);
    }

    /**
     * 更新知识库
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|array|max:10',
            'is_public' => 'boolean'
        ]);

        $knowledgeBase = $this->knowledgeBaseService->updateKnowledgeBase($user->id, $id, $validated);
        if (!$knowledgeBase) {
            return response()->json([
                'success' => false,
                'message' => '无权限操作或知识库不存在'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => '知识库更新成功',
            'data' => $knowledgeBase
        ]);
    }

    /**
     * 删除知识库
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $result = $this->knowledgeBaseService->deleteKnowledgeBase($user->id, $id);
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => '无权限操作或知识库不存在'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => '知识库删除成功'
        ]);
    }

    /**
     * 添加知识条目
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addEntry(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'knowledge_id' => 'required|integer|exists:knowledge,id',
            'category_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        $entry = $this->knowledgeBaseService->addEntry(
            $user->id,
            $id,
            $validated['knowledge_id'],
            $validated['category_id'] ?? null,
            $validated['notes'] ?? ''
        );

        return response()->json([
            'success' => true,
            'message' => '知识条目添加成功',
            'data' => $entry
        ], 201);
    }

    /**
     * 更新知识条目
     *
     * @param Request $request
     * @param int $id
     * @param int $entryId
     * @return JsonResponse
     */
    public function updateEntry(Request $request, int $id, int $entryId): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'category_id' => 'nullable|integer'
        ]);

        $entry = $this->knowledgeBaseService->updateEntry($user->id, $id, $entryId, $validated);
        if (!$entry) {
            return response()->json([
                'success' => false,
                'message' => '无权限操作或条目不存在'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => '知识条目更新成功',
            'data' => $entry
        ]);
    }

    /**
     * 删除知识条目
     *
     * @param Request $request
     * @param int $id
     * @param int $entryId
     * @return JsonResponse
     */
    public function removeEntry(Request $request, int $id, int $entryId): JsonResponse
    {
        $user = $request->user();
        $result = $this->knowledgeBaseService->removeEntry($user->id, $id, $entryId);
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => '无权限操作或条目不存在'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => '知识条目删除成功'
        ]);
    }

    /**
     * 添加分类
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addCategory(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'parent_id' => 'nullable|integer'
        ]);

        $category = $this->knowledgeBaseService->addCategory(
            $user->id,
            $id,
            $validated['name'],
            $validated['parent_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => '分类添加成功',
            'data' => $category
        ], 201);
    }

    /**
     * 删除分类
     *
     * @param Request $request
     * @param int $id
     * @param int $categoryId
     * @return JsonResponse
     */
    public function removeCategory(Request $request, int $id, int $categoryId): JsonResponse
    {
        $user = $request->user();
        $result = $this->knowledgeBaseService->removeCategory($user->id, $id, $categoryId);
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => '无权限操作或分类不存在'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => '分类删除成功'
        ]);
    }

    /**
     * 导出知识库
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function export(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $format = $request->input('format', 'json');
        $export = $this->knowledgeBaseService->export($user->id, $id, $format);
        if (!$export) {
            return response()->json([
                'success' => false,
                'message' => '无权限操作或知识库不存在'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => '导出成功',
            'data' => $export
        ]);
    }
}
