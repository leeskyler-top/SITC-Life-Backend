<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return $this->ok("已列出所有用户信息", $users->makeHidden(['token']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'avatar_url',
            'username',
            'firstname',
            'lastname',
            'notes',
            'is_admin'
        ]);
        $request->validate([
            'avatar_url' => 'required',
            'username' => [
                'required',
                Rule::unique('users', 'username')->where('deleted_at')
            ],
            'firstname' => 'required',
            'lastname' => 'required',
            'notes' => 'required',
            'is_admin' => 'required|string|in:0,1',
        ]);
        $data['avatar_url'] = Storage::put('images/avatar', $data['avatar_url']);
        $data['password'] = User::genPwd();
        $user = User::create($data);
        $user->refresh();
        $user->password = $data['password'];
        return $this->ok("用户添加完成", $user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->res('用户未找到', 404);
        }
        return $this->ok("用户已找到", $user->makeHidden(['token']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->res('用户未找到', 404);
        }
        $data = $request->only([
            'avatar_url',
            'firstname',
            'lastname',
            'notes',
            'is_admin'
        ]);

        $request->validate([
            'avatar_url' => 'nullable|string',
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_admin' => 'nullable|in:0,1|string',
        ]);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value === "0";
        });
        if (isset($data['is_admin']) && $data['is_admin'] === "0" && Auth::id() === $user->id) {
            return $this->res("出于系统安全相关的原因，不能取消自己的管理员权限", 400);
        }
        $user->fill($data)->save();
        $user->refresh();
        return $this->ok('用户信息变更成功', $user->makeHidden(['token']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->res('用户未找到', 404);
        }
        if ($user->id === Auth::id()) {
            return $this->res("管理员不能删除自己", 403);
        }
        $user->delete();
        return $this->ok("删除用户成功");
    }
}
