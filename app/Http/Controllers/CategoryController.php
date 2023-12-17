<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Category::with('books')->get();
            return response()->json(['data' => $categories], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching categories', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validateCategoryData($request);

            $category = Category::create($request->all());

            return response()->json(['message' => 'Category created successfully', 'data' => $category], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        try {
            $category->load('books');
            return response()->json(['data' => $category], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching category details', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        try {
            $this->validateCategoryData($request, $category->id);

            $category->update($request->all());

            return response()->json(['message' => 'Category updated successfully', 'data' => $category], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Validate category data.
     *
     * @param Request $request
     * @param int|null $categoryId
     */
    private function validateCategoryData(Request $request, ?int $categoryId = null): void
    {
        $rules = [
            'name' => 'required|unique:categories,name',
        ];

        if ($categoryId !== null) {
            $rules['name'] .= ',' . $categoryId;
        }

        $this->validate($request, $rules);
    }
}
