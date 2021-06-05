<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\MyCourse;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        $courses = Course::query();

        $q = $request->query('q');
        $status = $request->query('status');
        
        // check kondisi jika nama or q di cari
        $courses->when($q, function($query) use ($q) {
            return $query->whereRaw("name LIKE '%".strtolower($q)."%'");
        });

        // check kondisi jika status di cari
        $courses->when($status, function($query) use ($status){
            return $query->where('status', '=', $status);
        });

        return response()->json([
            'status' => 'Success',
            'data' => $courses->paginate(10)
        ]);
    }

    public function show($id)
    {
        // ambil data chaters dan function lesson pada model chapters, ambil pada model course pada function mentor dan images
        $course = Course::with('chapters.lessons')
                        ->with('mentor')
                        ->with('images')
                        ->find($id);

        if (!$course) {
            return response()->json([
                'status' => 'Error',
                'data' => $course
            ]);
        }

        // get review ketika input course id dan jadiin array
        $reviews = Review::where('course_id', '=', $id)->get()->toArray();

        // jika mengambil hanya user id untuk review
        if (count($reviews) > 0) {
            $userIds = array_column($reviews, 'user_id');
            $users = getUserByIds($userIds);
            // echo "<pre>".print_r($users, 1)."</pre>";

            // Jika Review kosong maka di hasilkan array kosong
            if ($users['status'] === 'Error') {
                $reviews = [];
            } else {
                // for untuk menghasilkan review sesuai dengan id
                foreach($reviews as $key => $review) {
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    $reviews[$key]['users'] = $users['data'][$userIndex];
                }
            }
        }

        // check total student
        $totalStudent = MyCourse::where('course_id', '=', $id)->count();

        // check total videos in course
        $totalVideos = Chapter::where('course_id', '=', $id)->withCount('lessons')->get()->toArray();

        // check final sum total videos
        $finalTotalVideos = array_sum(array_column($totalVideos, 'lessons_count'));


        $course['reviews'] = $reviews;
        $course['total_videos'] = $finalTotalVideos;
        $course['total_student'] = $totalStudent;

        return response()->json([
            'status' => 'Success',
            'data' => $course
        ]);
    }


    public function create(Request $request)
    {
        // schema validasi 
        $rules = [
            'name' => 'required|string',
            'certificate' => 'required|boolean',
            'thumbnail' => 'string|url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'integer',
            'level' => 'required|in:all-level,beginner,intermediate,advance',
            'mentor_id' => 'required|integer',
            'description' => 'string'
        ];

        $data = $request->all();

        // check validator
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        $mentorId = $request->input('mentor_id');
        $mentor = Mentor::find($mentorId);

        // check mentor tidak ada
        if (!$mentor) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Mentor Not Found'
            ], 404);
        }

        //  create data jika mentor ditemukan
        $course = Course::create($data);
        return response()->json([
            'status' => 'Success',
            'data' => $course
        ]);
    }

    public function update(Request $request, $id)
    {
        // schema validasi 
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'string|url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advance',
            'mentor_id' => 'integer',
            'description' => 'string'
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
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Sorry Course Not Found'
            ], 404);
        }

        $mentorId = $request->input('mentor_id');
        // check mentor Id
        if ($mentorId) {
            $mentor = Mentor::find($mentorId);
            if (!$mentor) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Mentor Not Found'
                ], 404);
            }
        }

        $course->fill($data);

        $course->save();
        return response()->json([
            'status' => 'Success',
            'data' => $course
        ]);
    }

    public function destroy($id)
    {
        $courseId = Course::find($id);

        if (!$courseId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Course Not Found'
            ], 404);
        }

        $courseId->delete();

        return response()->json([
            'status' => 'Success',
            'Message' => 'Course Deleted Successfully'
        ]);
    }
}
