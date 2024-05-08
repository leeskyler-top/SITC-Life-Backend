<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $semester = Semester::all();
        return $this->ok(Semester::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            "start_time",
            "end_time",
            "semester_name"
        ]);
        $request->validate([
            "start_time" => 'required|date_format:Y-m-d H:i:s',
            "end_time" => 'required|date_format:Y-m-d H:i:s',
            "semester_name = 'required"
        ]);
        $semester = Semester::create($data);
        $semester->refresh();
        return $this->ok("学期创建不成功", $semester);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $semester = Semester::find($id);
        if (!$semester) {
            return $this->res('学期未找到', 404);
        }
        return $this->ok("success", $semester);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $semester = Semester::find($id);
        if (!$semester) {
            return $this->res('学期未找到', 404);
        }
        $data = $request->only([
            "start_time",
            "end_time",
            "semester_name"
        ]);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value == 0;
        });
        $request->validate([
            "start_time" => 'nullable|date_format:Y-m-d H:i:s',
            "end_time" => 'nullable|date_format:Y-m-d H:i:s',
            "semester_name = 'nullable|string"
        ]);
        $semester->fill($data)->save();
        $semester->refresh();
        return $this->ok('变更成功', $semester);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $semester = Semester::find($id);
        if (!$semester) {
            return $this->res('学期未找到', 404);
        }
        return $this->ok();
    }
}
