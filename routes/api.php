<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {

//     Route::post('/logout', [AuthController::class, 'logout']);


//     Route::middleware('role:admin')->get('/admin', function () {
//         return response()->json(['message' => 'Welcome, Admin']);
//     });

//     Route::middleware('role:user')->get('/user', function () {
//         return response()->json(['message' => 'Welcome, User']);
//     });
// });
// Route::get('/users', [UserController::class, 'index']);
// Route::get('/users/{id}', [UserController::class, 'show']);
// Route::post('/users', [UserController::class, 'store']);
// Route::put('/users/{id}', [UserController::class, 'update']);
// Route::delete('/users/{id}', [UserController::class, 'destroy']); // Soft delete
// Route::patch('/users/{id}/restore', [UserController::class, 'restore']); // Restore user
// Route::delete('/users/{id}/force', [UserController::class, 'forceDelete']); // Hapus permanen
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->get('/admin', function () {
        return response()->json(['message' => 'Welcome, Admin']);
    });

    Route::middleware('role:user')->get('/user', function () {
        return response()->json(['message' => 'Welcome, User']);
    });


    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);


    Route::get('/roles', [UserController::class, 'role_list']);

    Route::get('/assignments', [AssignmentController::class, 'index']);

    Route::post('/assignments', [AssignmentController::class, 'store']);
    Route::put('/assignments/{id}', [AssignmentController::class, 'update']);
    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);
    Route::get('/assignments', [AssignmentController::class, 'index']);
});
