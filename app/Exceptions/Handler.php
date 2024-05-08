<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });
        $this->renderable(function (AuthenticationException $e) {
            return response()->json(['msg' => "未授权"], 401);
        });
        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json(['msg' => "试图访问的API未找到"], 404);
        });
        $this->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->json(['msg' => "此HTTP请求模式不被允许"], 405);
        });
        $this->renderable(function (TooManyRequestsHttpException $e) {
            return response()->json(['msg' => "请求过多"], 429);
        });
        $this->renderable(function (PostTooLargeException $e) {
            return response()->json(['msg' => "上传的数据超出限制，文件限制10M"], 413);
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['msg' => "unauthorized."], 401);
    }

    protected function shouldReturnJson($request, Throwable $e)
    {
        return true;
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $violations = Arr::map($exception->errors(), fn($error) => $error[0]);
        return response()->json([
            'msg' => "传递的数据格式非法",
            'error' => $violations
        ], 422);
    }


}
