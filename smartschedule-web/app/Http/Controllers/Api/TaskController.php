<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->tasks()->with('category')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'priority' => 'required|integer|min:1|max:5',
            'deadline' => 'required|date',
            'category_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                }),
            ],
            'status' => 'required|string|in:todo,in_progress,done',
        ]);

        $task = $request->user()->tasks()->create($validated);
        
        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($task->load('category'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'priority' => 'sometimes|required|integer|min:1|max:5',
            'deadline' => 'sometimes|required|date',
            'category_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('categories', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                }),
            ],
            'status' => 'sometimes|required|string|in:todo,in_progress,done',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $task->delete();
        return response()->json(null, 204);
    }
}
