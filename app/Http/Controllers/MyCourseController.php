<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{

    public function index(Request $request)
    {
        // get data with model Course 
        $myCourses = MyCourse::query()->with('course');

        // check if frontend input user id
        $userId = $request->query('user_id');
        $myCourses->when($userId, function($query) use ($userId){
            return $query->where('user_id', '=', $userId);
        });

        return response()->json([
            'status' => 'Success',
            'data' => $myCourses->get()
        ]);
    }

    public function create(Request $request)
    {
        // create validation rules
        $rules = [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        // check input course id by user
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Course Not Found'
            ], 404);
        }

        // check input user ID by user
        $userId = $request->input('user_id');
        $user = getUser($userId);

        if (!$user['status'] === 'Error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['http_code']);
        }

        $isExistMyCourse = MyCourse::where('course_id', '=', $courseId)
                                    ->where('user_id', '=', $userId)
                                    ->exists();

        if ($isExistMyCourse) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User Already Taken This Course'
            ], 409);
        }


        // check type course
        if ($course->type === 'premium') {
            if ($course->price === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Price can\'t be 0'
                ], 405);
            }
            
            $order = postOrder([
                'user' => $user['data'],
                'course' => $course->toArray()
            ]);

            // echo "<pre>".print_r($order, 1)."</pre>";

            if ($order['status'] === 'error') {
                return response()->json([
                    'status' => $order['status'],
                    'message' => $order['message']
                ], $order['http_code']);
            }

            return response()->json([
                'status' => $order['status'],
                'data' => $order['data']
            ]);
        } else {
            $myCourse = MyCourse::create($data);

            return response()->json([
                'status' => 'success',
                'data' => $myCourse
            ]);
        }
    }

    public function createPremiumAccess(Request $request)
    {
        $data = $request->all();
        $myCourse = MyCourse::create($data);

        return response()->json([
            'status' => 'Success',
            'data' => $myCourse
        ]);
    }
}
