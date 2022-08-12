<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $lesson = Lesson::query();

        $chapterId = $request->query('chapter_id');

        $lesson->when($chapterId, function ($query) use ($chapterId) {
            return $query->where("chapter_id", $chapterId);
        });


        return response()->json([
            'status' => 'success',
            'data' => $lesson->paginate(10)
        ]);
    }

    public function show($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $lesson
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
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $chapterId = $request->input('chapter_id');
        $chapter = Chapter::find($chapterId);

        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ]);
        }

        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'success',
            'message' => $lesson
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
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $chapterId = $request->input('chapter_id');
        if ($chapterId) {
            $chapter = Chapter::Find($chapterId);
            if (!$chapter) {
                return response()->json([
                    'status' => 'error ',
                    'data' => 'chapter not found'
                ]);
            }
        }

        $lesson->fill($data);
        $lesson->save();

        return response()->json([
            'status' => 'success ',
            'data' => $lesson
        ]);
    }

    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            'status' => 'success',
            'data' => 'lesson deleted'
        ]);
    }
}
