<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'succcess',
            'data' => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create($request->all());

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 404);
        }

        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 404);
        }
        $category->update($request->all());

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 404);
        }

        Category::destroy($id);
        return response()->json([], 204);
    }
}
