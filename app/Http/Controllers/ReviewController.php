<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\Review;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
       $rules = [
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'note' => 'string'
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

       if (!$course) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Course Not Found'
            ], 404);
       }

       $userId = $request->input('user_id');
       $user = getUser($userId);

        if (!$user['status'] === 'Error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['http_code']);
        }

        $isExistReview = Review::where('course_id', '=', $courseId)
                                ->where('user_id', '=', $userId)
                                ->exists();

        if ($isExistReview) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Review Already Exist'
            ], 409);
        }

        $review = Review::create($data);
        return response()->json([
            'status' => 'Success',
            'message' => $review
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'rating' => 'integer|min:1|max:5',
            'note' => 'string'
       ];

       // ambil data dari body kecuali user id dan course id
       $data = $request->except('user_id', 'course_id');

       $validator = Validator::make($data, $rules);

       if ($validator->fails()) {
           return response()->json([
               'status' => 'Error',
               'message' => $validator->errors()
           ], 400);
       }

       $review = Review::find($id);
       if (!$review) {
           return response()->json([
               'status' => 'Error',
               'message' => 'Review Not Found'
           ], 404);
       }

       $review->fill($data);
       $review->save();
        return response()->json([
            'status' => 'Success',
            'data' => $review
        ]);

    }

    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Review Not Found'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'status' => 'Success',
            'Message' => 'Review Deleted Successfully'
        ]);
    }
}
