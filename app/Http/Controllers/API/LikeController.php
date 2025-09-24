<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    // api to get likes and dislikes count for a post
    public function reactions(Request $request, $postId)
    {
        $likesCount = Like::where('post_id', $postId)->where('status', 1)->count();
        $dislikesCount = Like::where('post_id', $postId)->where('status', 2)->count();

        return response()->json([
            'status' => 'success',
            'likes' => $likesCount,
            'dislikes' => $dislikesCount,
            'post_id' => $postId,
        ], 200);
    }

    // likes and dislikes
    public function react(Request $request)
    {
        // validate
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:blog_posts,id',
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }
        $userId = auth()->id();
        $postId = $request->post_id;
        $status = $request->status;

        // if user has already reacted to the post, update the reaction
        $like = Like::where('user_id', $userId)->where('post_id', $postId)->first();
        if ($like) {
            if ($like->status == $status) {
                // same reaction, remove it
                $like->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reaction removed successfully'
                ], 201);
            } else {
                //update reaction 
                $like->status = $status;
                $like->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reaction updated successfully'
                ]);
            }
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $postId,
                'status' => $status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Reaction recorded successfully'
            ], 201);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
