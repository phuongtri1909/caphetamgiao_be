<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Franchise;
use App\Http\Resources\FranchiseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FranchiseController extends Controller
{
    /**
     * Lấy danh sách tất cả gói nhượng quyền với phân trang và filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Franchise::query();

            // Sắp xếp theo thứ tự hiển thị hoặc ngày tạo
            $sortField = $request->input('sort_by', 'sort_order');
            $sortDirection = $request->input('sort_direction', 'asc');
            $allowedSortFields = ['name', 'sort_order', 'created_at'];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('sort_order', 'asc');
            }

            // Phân trang
            $perPage = min(max((int)$request->input('per_page', 10), 1), 50); // Giới hạn 1-50 items/page
            $franchises = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'current_page' => $franchises->currentPage(),
                    'data' => FranchiseResource::collection($franchises),
                    'first_page_url' => $franchises->url(1),
                    'from' => $franchises->firstItem(),
                    'last_page' => $franchises->lastPage(),
                    'last_page_url' => $franchises->url($franchises->lastPage()),
                    'links' => $franchises->linkCollection()->toArray(),
                    'next_page_url' => $franchises->nextPageUrl(),
                    'path' => $franchises->path(),
                    'per_page' => $franchises->perPage(),
                    'prev_page_url' => $franchises->previousPageUrl(),
                    'to' => $franchises->lastItem(),
                    'total' => $franchises->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một gói nhượng quyền
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $franchise = Franchise::where('slug', $slug)
                ->firstOrFail();

            // Lấy các gói nhượng quyền khác để so sánh
            $otherFranchises = Franchise::where('id', '!=', $franchise->id)
                ->orderBy('sort_order', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'franchise' => new FranchiseResource($franchise),
                    'other_franchises' => FranchiseResource::collection($otherFranchises)
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy gói nhượng quyền'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}