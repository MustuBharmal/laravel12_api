<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $post = BlogPost::get();

        return response()->json([
            'status' => 'success',
            'count' => count($post),
            'data' => $post,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric|exists:blog_categories,id',
            'title' => 'required|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'messages' => $validator->errors(),
            ], 400);
        }

        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();

        if ($loggedInUser->id != $request->user_id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Un-authorized access'
            ], 400);
        }
        // check if the category id exists
        // handled by validation rule 'exists:blog_categories,id'

        $imagePath = null;
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $image = $request->file('thumbnail');

            // Generate a unique filename
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Store the image in the 'public/blog_thumbnails' directory
            $image->move(public_path('storage/posts'), $imageName);

            // save the path to the database

            $imagePath = "storage/posts/" . $imageName;
        }
        $data['title'] = $request->title;
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = $request->user_id;
        $data['category_id'] = $request->category_id;
        $data['content'] = $request->content;
        $data['excerpt'] = $request->excerpt;
        $data['thumbnail'] = $imagePath ? $imagePath : null;

        if ($loggedInUser->role == 'admin') {
            $data['status'] = 'published';
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        BlogPost::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'New blog post has been created successfully',
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
        $post = BlogPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Blog post not found',
            ], 404);
        }

        // validator input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric|exists:blog_categories,id',
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'messages' => $validator->errors(),
            ], 400);
        }
        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();

        // if ($loggedInUser->id != $request->user_id) {
        //     return response()->json([
        //         'status' => 'fail',
        //         'message' => 'Un-authorized access'
        //     ], 400);
        // }

        //check if the category id exists

        // check additional condition to restrict authorized edit

        if ($loggedInUser->role == 'admin' && $post->user_id == $loggedInUser->id) {

            $post->user_id = $request->user_id;
            $post->category_id = $request->category_id;
            $post->title = $request->title;
            $post->slug = Str::slug($request->title);
            $post->content = $request->content;
            $post->excerpt = $request->excerpt;
            $post->status = $request->status;

            $post->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Blog post has been updated successfully',
            ], 201);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to edit this blog post',
            ], 400);
        }
    }

    public function blogPostImage(Request $request, int $id)
    {
        $post = BlogPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Blog post not found',
            ], 404);
        }

        //  validate the image
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'thumbnail' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'messages' => $validator->errors(),
            ], 400);
        }

        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();

        // if ($loggedInUser->id != $post->user_id) {
        //     return response()->json([
        //         'status' => 'fail',
        //         'message' => 'Un-authorized access'
        //     ], 400);
        // }

        // image upload
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $image = $request->file('thumbnail');

            // Generate a unique filename
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Store the image in the 'public/blog_thumbnails' directory
            $image->move(public_path('storage/posts'), $imageName);

            // save the path to the database

            $imagePath = "storage/posts/" . $imageName;

            $post->thumbnail = isset($imagePath) ? $imagePath : $post->thumbnail;

            $post->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Blog post image has been uploaded successfully',
                'data' => [
                    'thumbnail' => $imagePath,
                ],
            ], 201);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to perforn this task',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $post = BlogPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Blog post not found',
            ], 404);
        }

        $loggedInUser = Auth::user();
        if ($loggedInUser->role == 'admin' && $post->user_id == $loggedInUser->id) {
            $post->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Blog post has been deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to delete this blog post',
            ], 403);
        }
    }
}
