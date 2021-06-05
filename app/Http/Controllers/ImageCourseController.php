<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageCourse;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'image' => 'required|url',
            'course_id' => 'required|integer',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        // check mentor tidak ada
        if (!$course) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Course Not Found'
            ], 404);
        }

        //  create data jika mentor ditemukan
        $imageCourse = ImageCourse::create($data);
        return response()->json([
            'status' => 'Success',
            'data' => $imageCourse
        ]);
    }

    public function destroy($id)
    {
        $imageCourseId = ImageCourse::find($id);

        if (!$imageCourseId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Image Course Not Found'
            ], 404);
        }

        $imageCourseId->delete();

        return response()->json([
            'status' => 'Success',
            'Message' => 'Course Image Deleted Successfully'
        ]);
    }
}
