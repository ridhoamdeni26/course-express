<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{

    public function index()
    {
        $mentors = Mentor::all();
        return response()->json([
            'status' => 'Success',
            'data' => $mentors
        ]);
    }

    public function show($id)
    {
        $find = Mentor::find($id);
        if (!$find) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Mentor Not Found'
            ]);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $find
        ]);
    }

    public function create(Request $request)
    {
        // schema validasi 
        $rules = [
            'name' => 'required|string',
            'profile' => 'required|url',
            'profession' => 'required|string',
            'email' => 'required|email'
        ];


        // mengambil seluruh data dari body
        $data = $request->all();

        // check validator
        $validator = Validator::make($data, $rules);

        // check validasi error or not
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        // create data
        $mentor = Mentor::create($data);

        return response()->json([
            'status' => 'Success',
            'data' => $mentor
        ]);
    }

    public function update(Request $request, $id)
    {
        // schema validasi 
        $rules = [
            'name' => 'string',
            'profile' => 'url',
            'profession' => 'string',
            'email' => 'email'
        ];


        // mengambil seluruh data dari body
        $data = $request->all();

        // check validator
        $validator = Validator::make($data, $rules);

        // check validasi error or not
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        // create data
        $mentor = Mentor::find($id);

        if (!$mentor) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Mentor Not Found'
            ], 404);
        }

        $mentor->fill($data);

        $mentor->save();
        return response()->json([
            'status' => 'Success',
            'data' => $mentor
        ]);
    }

    public function destroy($id)
    {
        $mentors = Mentor::find($id);

        if (!$mentors) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Mentor Not Found'
            ], 404);
        }

        $mentors->delete();
        return response()->json([
            'status' => 'Success',
            'data' => 'Deleted Mentor Successfully'
        ]);
    }
}
