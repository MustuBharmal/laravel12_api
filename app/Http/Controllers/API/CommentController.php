<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $comments = Comment::get();

        return response()->json([
            'status' => 'success',
            'count' => $comments->count(),
            'data' => $comments,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validate = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:blog_posts,id',
            'content' => 'required|string|max:1000',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validate->errors(),
            ], 400);
        }

        $data['post_id'] = $request->post_id;
        $data['user_id'] = auth()->id();
        $data['content'] = $request->content;

        Comment::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created and waiting for the admin approval',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $comment = Comment::where('post_id', $id)->where('status', 'approved')->get();
        if ($comment) {
            return response()->json([
                'status' => 'success',
                'data' => $comment,
                'count' => $comment->count(),
            ], 200);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'No comment found for this post',
        ], 404);
    }

    // change status
    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        $comment = Comment::find($request->comment_id);
        $comment->status = $request->status;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment status changed successfully',
        ], 201);
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
