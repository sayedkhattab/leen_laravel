<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
     * Store a newly created subcategory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|unique:sub_categories',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $input = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('subcategories', 'public');
            $input['image'] = $imagePath;
        }

        $subcategory = SubCategory::create($input);

        return $this->sendResponse($subcategory, 'Subcategory created successfully.', 201);
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

    /**
     * Update the specified subcategory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubCategory  $subcategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, SubCategory $subcategory): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|unique:sub_categories,name,' . $subcategory->id,
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        if ($request->has('category_id')) {
            $subcategory->category_id = $request->category_id;
        }

        if ($request->has('name')) {
            $subcategory->name = $request->name;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }
            $imagePath = $request->file('image')->store('subcategories', 'public');
            $subcategory->image = $imagePath;
        }

        $subcategory->save();

        return $this->sendResponse($subcategory, 'Subcategory updated successfully.');
    }

    /**
     * Remove the specified subcategory from storage.
     *
     * @param  \App\Models\SubCategory  $subcategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SubCategory $subcategory): JsonResponse
    {
        // Check if subcategory has services
        if ($subcategory->homeServices()->count() > 0 || $subcategory->studioServices()->count() > 0) {
            return $this->sendError('Cannot delete subcategory with services.', [], 422);
        }

        // Delete subcategory image if exists
        if ($subcategory->image) {
            Storage::disk('public')->delete($subcategory->image);
        }

        $subcategory->delete();

        return $this->sendResponse([], 'Subcategory deleted successfully.');
    }
} 