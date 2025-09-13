<?php
namespace App\Http\Controllers\API;
use App\Models\Student;
use Illuminate\Support\Facades\Validator; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $student = Student::get();

       return response()->json([
        'status' => 'success',
        'students' => $student,
       ], status: 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|unique:students,email',
            'gender' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors(),
            ],status: 400);
        }
        $data = $request->all();
        Student::create($data); // it will store data in database table 'students'

        return response()->json([
            'status' => 'success',
            'message' => 'Student created successfully',
        ],status: 201);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::find($id);

        if($student){
            return response()->json([
                'status' => 'success',
                'data' => $student,
            ], status: 200);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'Student not found',
        ], status: 404);    
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|unique:students,email,'.$id,
            'gender'=> 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors(),
            ],status: 400);
        }
    
        $student = Student::find($id);
        if(!$student){
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found',
            ],status: 404);
        }
        $student->name = $request->name;
        $student->email = $request->email;
        $student->gender = $request->gender;
        $student->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Student updated successfully',
            'data'=>$student,
        ],status: 200);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);
        if(!$student){
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found',
            ],status: 404);
        }
        $student->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Student deleted successfully',
        ],status: 201);
    }
}
