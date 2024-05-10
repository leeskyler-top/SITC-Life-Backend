<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function ok($msg = null, $data = null)
    {
        return !$data
            ? (!$msg ? response()->json(['msg' => "success"], 200) : response()->json(['msg' => $msg], 200))
            : response()->json(['msg' => $msg, 'data' => $data], 200);
    }

    public function res($msg, $code)
    {
        return response()->json(['msg' => $msg], $code);
    }

}
