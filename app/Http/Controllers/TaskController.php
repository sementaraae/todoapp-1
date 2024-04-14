<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function toggleCompleted($taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->is_completed = !$task->is_completed;
        $task->save();
        
        return redirect()->back(); 
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string',
            'card_id' => 'required|exists:cards,id'
        ]);

        Task::create($validatedData);

        return redirect()->back(); 
    }

    public function update(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        $validatedData = $request->validate([
            'description' => 'sometimes|required|string',
            'is_completed' => 'sometimes|required|boolean',
            'card_id' => 'sometimes|required|exists:cards,id'
        ]);

        $task->update($validatedData);

        return redirect()->back(); 
    }
}
