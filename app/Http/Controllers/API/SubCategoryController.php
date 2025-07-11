<?php

namespace App\Http\Controllers\API;

use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubCategoryController extends BaseController
{
    /**
     * Display a listing of the subcategories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $subcategories = SubCategory::with('category')->get();

        return $this->sendResponse($subcategories, 'Subcategories retrieved successfully.');
    }

    /**
     * Display the specified subcategory.
     *
     * @param  \App\Models\SubCategory  $subcategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SubCategory $subcategory): JsonResponse
    {
        $subcategory->load('category');

        return $this->sendResponse($subcategory, 'Subcategory retrieved successfully.');
    }
} 