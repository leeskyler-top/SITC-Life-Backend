<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return $this->ok("success", $users->makeHidden(['token']));
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
                'email:filter',
                Rule::unique('users', 'username')->where('deleted_at')
            ],
            'firstname' => 'required',
            'lastname' => 'required',
            'notes' => 'required',
            'is_admin' => 'required|integer|in:0,1',
        ]);
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
        return $this->ok("success", $user->makeHidden(['token']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->jsonRes(404, '用户未找到');
        }
        $data = $request->only([
            'avatar_url',
            'firstname',
            'lastname',
            'notes',
            'is_admin'
        ]);
        $data = array_filter($data, function ($value) {
            return !empty($value) || $value == 0;
        });
        $request->validate([
            'avatar_url' => 'nullable|string',
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_admin' => 'nullable|integer|in:0,1',
        ]);
        if (isset($data['is_admin']) && $data['is_admin'] === '0' && Auth::id() === $user->id) {
            return $this->jsonRes(400, "出于系统安全相关的原因，不能取消自己的管理员权限");
        }
        $user->fill($data)->save();
        $user->refresh();
        return $this->ok('变更成功', $user->makeHidden(['token']));
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
        $this->ok();
    }
}
