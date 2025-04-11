<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Lấy danh sách tất cả danh mục
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $categories = Category::orderBy('sort_order')->get();
            
            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một danh mục
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $category = Category::where('slug', $slug)
                ->select('id', 'name', 'slug', 'sort_order')
                ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }
    }

    /**
     * Lấy danh sách sản phẩm theo danh mục
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function products($slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            
            $products = $category->products()
                ->with(['weights' => function($query) {
                    $query->where('is_active', true);
                }])
                ->where('is_active', true)
                ->select('id', 'category_id', 'name', 'slug', 'description', 'highlight', 'image', 'is_featured')
                ->get()
                ->map(function($product) {
                    $product->min_price = $product->min_price;
                    $product->min_discounted_price = $product->min_discounted_price;
                    return $product;
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'products' => $products
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }
    }
}