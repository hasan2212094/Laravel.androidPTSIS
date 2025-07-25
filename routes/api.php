<?php

use Illuminate\Http\Request;
use App\Exports\QualityExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\NotificationController;

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

Route::post('/send-notification', [NotificationController::class, 'send']);

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
    Route::get('/assignments/summary', [AssignmentController::class, 'show_summary']);
    Route::get('/assignments/all', [AssignmentController::class, 'show_all']);
    Route::get('/assignments/export', [AssignmentController::class, 'exportSummary']);
    Route::get('/assignments/{id}', [AssignmentController::class, 'show']);

    Route::post('/assignments', [AssignmentController::class, 'store']);
    Route::post('/assignments/updatestatus/{id}', [AssignmentController::class, 'updatestatus']);

    Route::put('/assignments/updatepembuat/{id}', [AssignmentController::class, 'updatePembuat']);
    // Route::put('/assignments/updatepenerima/{id}', [AssignmentController::class, 'updateEnd']);

    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']); // Soft delete
    Route::get('/assignmentsdata', [AssignmentController::class, 'indexdelete']); // Lihat data terhapus
    Route::post('/assignments/{id}/restore', [AssignmentController::class, 'restore']); // Restore data
    Route::delete('/assignments/forcedelete/{id}', [AssignmentController::class, 'forceDelete']); // Hapus permanen

    Route::get('/assignments/count/{id}', [AssignmentController::class, 'countAssignments']);

    Route::get('/assignments', [AssignmentController::class, 'index']);


    Route::get('/qualities', [QualityController::class, 'index']); // ambil semua
    Route::post('/qualities', [QualityController::class, 'store']); // simpan baru

    Route::get('/qualities/{quality}', [QualityController::class, 'show']);     // detail
    Route::post('qualities/{quality}', [QualityController::class, 'update']);    // update

    Route::get('/qualitiesdelete', [QualityController::class, 'indexdelete']); // ambil semua
    Route::delete('/qualities/{quality}', [QualityController::class, 'destroy']); // hapus sementara
    Route::put('/qualities/restore/{id}', [QualityController::class, 'restore']); //kembalikan data yang hilang
    Route::delete('/qualities/force-delete/{id}', [QualityController::class, 'forceDelete']); //hapus permanet

    Route::get('/qualities/viewers/{qualityId}', [QualityController::class, 'showViewer']);
    Route::post('/qualities/viewers/{qualityId}', [QualityController::class, 'storeViewer']);

    Route::post('/qualities/updaterelevanstatus/{id}', [QualityController::class, 'updaterelevanstatus']);
    Route::post('/qualities/updatenotrelevanstatus/{id}', [QualityController::class, 'updatenotrelevanstatus']);

    Route::post('/qualities/updatedone/{id}', [QualityController::class, 'updatedone']);
    Route::post('/qualities/updateinprogress/{id}', [QualityController::class, 'updateinprogress']);

    Route::get('/quality/export', [QualityController::class, 'exportSummary'])->name('quality.export');
    Route::get('/work-orders', [QualityController::class, 'workorder_list']);
    //    Route::post('/send-notification', function (Request $request) {
    //     Log::info('Notifikasi diterima: ' . $request->message);
    //     return response()->json(['status' => 'success', 'data' => $request->all()]);
    // });

});
