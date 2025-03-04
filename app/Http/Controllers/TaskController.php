<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks,title'
        ]);

        $task = Task::create([
            'title' => $request->title
        ]);

        return response()->json($task);
    }

    public function update(Task $task)
    {
        $task->update([
            'completed' => !$task->completed
        ]);

        return response()->json($task);
    }
    
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }
}
