<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
{
    if ($exception instanceof ModelNotFoundException) {
        return response()->json([
            'message' => 'Data tidak ditemukan!'
        ], 404);
    }

    if ($exception instanceof ValidationException) {
        return response()->json([
            'message' => 'Validasi gagal!',
            'errors' => $exception->errors()
        ], 422);
    }

    return response()->json([
        'message' => 'Terjadi kesalahan pada server!',
        'error' => $exception->getMessage()
    ], 500);
}
}
