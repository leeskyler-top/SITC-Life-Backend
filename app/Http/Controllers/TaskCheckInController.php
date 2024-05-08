<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCheckInResource;
use App\Models\TaskCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskCheckInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taskCheckIns = TaskCheckIn::all();
        return $this->ok("success", TaskCheckInResource::collection($taskCheckIns));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            "task_id",
            "user_id",
            "afl_type",
            "afl_url",
            "afl_status"
        ]);
        $request->validate([
            "task_id" => 'required|exists:tasks,id|integer',
            "user_id" => 'required|exists:users,id|integer',
            "afl_type" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->has('afl_url') || $request->has('afl_status');
                }),
            ],
            "afl_url" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->has('afl_type') || $request->has('afl_status');
                }),
            ],
            "afl_status" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->has('afl_type') || $request->has('afl_url');
                }),
                Rule::in(['agreed', 'rejected'])
            ]
        ]);
        if (isset($data['afl_type'])) {
            $data['audit_id'] = Auth::id();
        }
        $taskCheckIns = TaskCheckIn::create($data);
        return $this->ok("补录或创建任务签到成功", $taskCheckIns);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $taskCheckIns = TaskCheckIn::find($id);
        if (!$taskCheckIns) {
            return $this->res("任务签到未找到", 404);
        }
        return $this->ok("签到查找成功", new TaskCheckInResource($taskCheckIns));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $taskCheckIns = TaskCheckIn::find($id);
        if (!$taskCheckIns) {
            return $this->res("任务签到未找到", 404);
        }
        $data = $request->only([
            "task_id",
            "user_id",
            "check_in_time",
            "check_in_url",
            "afl_type",
            "afl_url",
            "afl_status"
        ]);
        $request->validate([
            "task_id" => 'required|exists:tasks,id|integer',
            "user_id" => 'required|exists:users,id|integer',
            "afl_type" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->has('afl_url') || $request->has('afl_status');
                }),
            ],
            "afl_url" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->has('afl_type') || $request->has('afl_status');
                }),
            ],
            "afl_status" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->has('afl_type') || $request->has('afl_url');
                }),
                Rule::in(['agreed', 'rejected'])
            ]
        ]);
        if (isset($data['afl_type'])) {
            $data['audit_id'] = Auth::id();
        }
        $taskCheckIns->fill($data)->save();
        $taskCheckIns->refresh();
        return $this->ok("任务签到变更成功", $taskCheckIns);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $taskCheckIns = TaskCheckIn::find($id);
        if (!$taskCheckIns) {
            return $this->res("任务签到未找到", 404);
        }
        $taskCheckIns->delete();
        return $this->ok();
    }
}
