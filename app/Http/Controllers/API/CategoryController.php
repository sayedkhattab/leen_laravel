<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return $this->sendResponse($categories, 'Categories retrieved successfully.');
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        return $this->sendResponse($category, 'Category retrieved successfully.');
    }

    /**
     * Display the subcategories of the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function subcategories(Category $category): JsonResponse
    {
        $subcategories = $category->subCategories;

        return $this->sendResponse($subcategories, 'Subcategories retrieved successfully.');
    }
} 