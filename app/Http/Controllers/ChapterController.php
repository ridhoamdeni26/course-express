<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{

    public function index(Request $request)
    {
       $chapters = Chapter::query();
       // ambil data untuk seleksi chapter id
       $courseId = $request->query('course_id');

       $chapters->when($courseId, function($query) use ($courseId){
            return $query->where('course_id', '=', $courseId);
       });

       return response()->json([
            'status' => 'Success',
            'data' => $chapters->get()
        ]);
    }

    public function show($id)
    {
        $chapterId = Chapter::find($id);

        if (!$chapterId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Chapter Not Found'
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'message' => $chapterId
        ]);
    }


    public function create(Request $request)
    {
        // get rules for validaition
        $rules = [
            'name' => 'required|string',
            'course_id' =>'required|integer'
        ];

        // check data request input
        $data = $request->all();

        // check validator for data
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }


        // check course id
        $courseId = $request->input('course_id');
        // find course id in Course Model
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Course Not Found'
            ], 404);
        }

        // Save Data
        $chapter = Chapter::create($data);
        return response()->json([
            'status' => 'Success',
            'data' => $chapter
        ]);
    }

    public function update(Request $request, $id)
    {
        // get rules for validaition
        $rules = [
            'name' => 'string',
            'course_id' =>'integer'
        ];

        // check data request input
        $data = $request->all();

        // check validator for data
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        // check Chapter id
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Chapter Not Found'
            ], 404);
        }

        // Check Capter course cause Course berhubungan dengan Chapter
        $courseId = $request->input('course_id');
        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Course Not Found'
                ], 404);
            }
        }

        // Update fill dari Chapter dan save
        $chapter->fill($data);
        $chapter->save();

        return response()->json([
            'status' => 'Success',
            'data' => $chapter
        ]);
    }

    public function destroy($id)
    {
        $chapterId = Chapter::find($id);

        if (!$chapterId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Chapter Not Found'
            ], 404);
        }

        $chapterId->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Chapter Deleted Successfully'
        ]);
    }
}
