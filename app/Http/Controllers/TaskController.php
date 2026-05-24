<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Show the Kanban task board.
     */
    public function board()
    {
        // TODO: fetch tasks grouped by status for the logged-in user
        return view('tasks.board');
    }

    /**
     * Store a new task.
     */
    public function store(Request $request)
    {
        // TODO: validate, create task, return JSON response for the board JS
        $request->validate([
            'title'    => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
        ]);

        return response()->json(['message' => 'Task created'], 201);
    }

    /**
     * Update a task (title, status, priority, etc).
     */
    public function update(Request $request, $task)
    {
        // TODO: find task, authorize, update fields, return updated task
        return response()->json(['message' => 'Task updated']);
    }

    /**
     * Delete a task.
     */
    public function destroy($task)
    {
        // TODO: find task, authorize, soft-delete or hard delete
        return response()->json(['message' => 'Task deleted']);
    }
}
