<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{

    public function index(Request $request)
    {
        $lessons = Lesson::query();

        $chapterId = $request->query('chapter_id');

        $lessons->when($chapterId, function($query) use ($chapterId){
            return $query->where('chapter_id', '=', $chapterId);
        });

        return response()->json([
            'status' => 'Success',
            'data' => $lessons->get()
        ]);
    }

    public function show(Request $request, $id)
    {
        $lessonId = Lesson::find($id);

        if (!$lessonId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Lesson Not Found'
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'message' => $lessonId
        ]);
    }

    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'video' => 'required|string',
            'chapter_id' => 'required|integer'
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        $chapterId = $request->input('chapter_id');
        $chapter = Chapter::find($chapterId);

        if (!$chapter) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Lesson Not Found'
            ], 404);
        }

        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'Success',
            'data' => $lesson
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'string',
            'video' => 'string',
            'chapter_id' => 'integer'
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => $validator->errors()
            ], 400);
        }

        $lessonId = Lesson::find($id);
        if (!$lessonId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Lesson Not Found'
            ], 404);
        }

        $chapterId = $request->input('chapter_id');
        if ($chapterId) {
            $chapter = Chapter::find($chapterId);
            if (!$chapter) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Chapter Not Found'
                ], 404);
            }
        }

        $lessonId->fill($data);
        $lessonId->save();
        return response()->json([
            'status' => 'Success',
            'message' => $lessonId
        ]);

    }

    public function destroy($id)
    {
        $lessonId = Lesson::find($id);

        if (!$lessonId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Lesson Not Found'
            ], 404);
        }

        $lessonId->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Lesson Deleted Successfully'
        ]);
    }
}
