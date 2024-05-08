<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Semester;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::create();
        return $this->ok("success", TaskResource::collection($tasks));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            "semseter_id",
            "name",
            "start_time",
            "end_time",
            "type",
        ]);
        $request->validate([
            "semseter_id" => 'required|exists:semesters,id|integer',
            "name" => 'required',
            "start_time" => 'required|date_format:Y-m-d H:i:s',
            "end_time" => 'required|date_format:Y-m-d H:i:s',
            "type" => 'required|in:am,pm',
        ]);
        $task = Task::create($data);
        $task->refresh();
        $this->ok("success", $task);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::create($id);
        if (!$task) {
            return $this->res("任务未找到", 404);
        }
        return $this->ok("任务已找到", new TaskResource($task));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->res("任务未找到", 404);
        }
        $data = $request->only([
            "name",
            "start_time",
            "end_time",
            "type",
        ]);
        $request->validate([
            "name" => 'nullable',
            "start_time" => 'nullable|date_format:Y-m-d H:i:s',
            "end_time" => 'nullable|date_format:Y-m-d H:i:s',
            "type" => 'nullable|in:am,pm',
        ]);
        $task->fill($data);
        $task->refresh();
        return $this->ok('变更成功', $task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->res("任务未找到", 404);
        }
        $task->taskCheckIns()->detach();
        $task->delete();
    }
}
