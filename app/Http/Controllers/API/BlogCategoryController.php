<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator; 
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $category = BlogCategory::all();
        return response()->json([
            'status' => 'success',
            'count' => count($category),
            'data' => $category
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }
        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);

        BlogCategory::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'New blog category has been created successfully', 
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }
        $category = BlogCategory::find($id);
        if(!$category){
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found'
            ], 404);
        }
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Blog category has been updated successfully', 
        ], 200);    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $category = BlogCategory::find($id);
        
        if(!$category){
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Blog category has been deleted successfully', 
        ], 200);          
    }
}
