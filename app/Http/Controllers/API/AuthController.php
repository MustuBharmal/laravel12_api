<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    //Registeration API
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $data = $request->all();

        // Image Upload
        $imagePath = null;
        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            $image = $request->file('profile_picture');

            // Generate a unique filename
            $filename = time() . '_' . $image->getClientOriginalName();

            // Move file to public directory
            $image->move(public_path('storage/profile'), $filename);

            // save the relative path in the database
            $imagePath = 'storage/profile/' . $filename;
        }
        $data['profile_picture'] = $imagePath;

        User::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'New user has been registered successfully',
        ], 201);
    }

    // Login API
    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response['token'] =  $user->createToken('BlogApp')->plainTextToken;
            $response['name'] =  $user->name;
            $response['email'] =  $user->email;

            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => $response
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid credentials'
            ], 400);
        }
    }

    // Profile API
    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    // Logout API
    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
