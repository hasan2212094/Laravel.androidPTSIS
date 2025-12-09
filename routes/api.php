<?php

use App\Models\Fabrikasi;
use Illuminate\Http\Request;
use App\Exports\QualityExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\KomponenController;
use App\Http\Controllers\PaintingController;
use App\Http\Controllers\FabrikasiController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\WorkorderController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ElectricalController;
use App\Http\Controllers\MaintenanceController;
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
    
    Route::get('/maintenance/export', [MaintenanceController::class, 'export']);
    Route::post('/maintenances', [MaintenanceController::class, 'store']);
    Route::get('/maintenances', [MaintenanceController::class, 'index']);
    Route::put('/maintenances/{id}', [MaintenanceController::class, 'update']);
    Route::get('/maintenances/{id}', [MaintenanceController::class, 'show']);
    Route::post('/maintenances/updatedone/{id}',[MaintenanceController::class, 'updatedone']);
    Route::get('/maintenancesdelete', [MaintenanceController::class, 'indexdelete']); // ambil semua
    Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy']); // hapus sementara
    Route::put('/maintenances/restore/{id}', [MaintenanceController::class, 'restore']); //kembalikan data yang hilang
    Route::delete('/maintenances/force-delete/{id}', [MaintenanceController::class, 'forceDelete']); //hapus permanet
    Route::get('/equipment', [MaintenanceController::class, 'equipment_list']);

    Route::get('/fabrikasi/export', [FabrikasiController::class, 'export']); 
    Route::get('/fabrikasi', [FabrikasiController::class, 'index']);
    Route::post('/fabrikasi', [FabrikasiController::class, 'store']);
    Route::get('/fabrikasi/{id}', [FabrikasiController::class, 'show']);
    Route::put('/fabrikasi/{id}', [FabrikasiController::class, 'update']);
    Route::post('/fabrikasi/updatedone/{id}',[FabrikasiController::class, 'updatedone']);
    Route::get('/fabrikasidelete', [FabrikasiController::class, 'indexdelete']); // ambil semua
    Route::delete('/fabrikasi/{fabrikasi}', [FabrikasiController::class, 'destroy']); // hapus sementara
    Route::put('/fabrikasi/restore/{id}', [FabrikasiController::class, 'restore']); //kembalikan data yang hilang
    Route::delete('/fabrikasi/force-delete/{id}', [FabrikasiController::class, 'forceDelete']); //hapus permanet
    Route::get('/work-orders', [FabrikasiController::class, 'workorder_list']);
    Route::get('/unit', [FabrikasiController::class, 'unit_list']);

    Route::get('/komponen/export', [KomponenController::class, 'export']); 
    Route::get('/komponen', [KomponenController::class, 'index']);
    Route::post('/komponen', [KomponenController::class, 'store']);
    Route::get('/komponen/{id}', [KomponenController::class, 'show']);
    Route::put('/komponen/{id}', [KomponenController::class, 'update']);
    Route::post('/komponen/updatedone/{id}',[KomponenController::class, 'updatedone']);
    Route::get('/komponendelete', [KomponenController::class, 'indexdelete']); // ambil semua
    Route::delete('/komponen/{komponen}', [KomponenController::class, 'destroy']); // hapus sementara
    Route::put('/komponen/restore/{id}', [KomponenController::class, 'restore']); //kembalikan data yang hilang
    Route::delete('/komponen/force-delete/{id}', [KomponenController::class, 'forceDelete']); //hapus permanet
    Route::get('/work-orders', [KomponenController::class, 'workorder_list']);
    Route::get('/unit1', [KomponenController::class, 'unit_list']);

    Route::get('/painting/export', [PaintingController::class, 'export']); 
    Route::get('/painting', [PaintingController::class, 'index']);
    Route::post('/painting', [PaintingController::class, 'store']);
    Route::get('/painting/{id}', [PaintingController::class, 'show']);
    Route::put('/painting/{id}', [PaintingController::class, 'update']);
    Route::post('/painting/updatedone/{id}',[PaintingController::class, 'updatedone']);
    Route::get('/paintingdelete', [PaintingController::class, 'indexdelete']); // ambil semua
    Route::delete('/painting/{painting}', [PaintingController::class, 'destroy']); // hapus sementara
    Route::put('/painting/restore/{id}', [PaintingController::class, 'restore']); //kembalikan data yang hilang
    Route::delete('/painting/force-delete/{id}', [PaintingController::class, 'forceDelete']); //hapus permanet
    Route::get('/work-orders', [PaintingController::class, 'workorder_list']);
    Route::get('/unit2', [PaintingController::class, 'unit_list']);

    Route::get('/electrical/export', [ElectricalController::class, 'export']); 
    Route::get('/electrical', [ElectricalController::class, 'index']);
    Route::post('/electrical', [ElectricalController::class, 'store']);
    Route::get('/electrical/{id}', [ElectricalController::class, 'show']);
    Route::put('/electrical/{id}', [ElectricalController::class, 'update']);
    Route::post('/electrical/updatedone/{id}',[ElectricalController::class, 'updatedone']);
    Route::get('/electricaldelete', [ElectricalController::class, 'indexdelete']); // ambil semua
    Route::delete('/electrical/{electrical}', [ElectricalController::class, 'destroy']); // hapus sementara
    Route::put('/electrical/restore/{id}', [ElectricalController::class, 'restore']); //kembalikan data yang hilang
    Route::delete('/electrical/force-delete/{id}', [ElectricalController::class, 'forceDelete']); //hapus permanet
    Route::get('/work-orders', [ElectricalController::class, 'workorder_list']);
    Route::get('/unit3', [ElectricalController::class, 'unit_list']);

    Route::get('/workorder', [WorkorderController::class, 'index']);
    Route::post('/workorder', [WorkorderController::class, 'store']);
    Route::get('/workorder/{id}', [WorkorderController::class, 'show']);
    Route::put('/workorder/{id}', [WorkorderController::class, 'update']);
    Route::delete('/workorder/{id}',[WorkorderController::class, 'destroy']);

    Route::get('/messenger',[MessengerController::class, 'index']);
    Route::post('/messenger',[MessengerController::class, 'store']);
    Route::get('/messenger/{id}',[MessengerController::class,'show']);
    Route::put('/messenger/{id}',[MessengerController::class,'update']);
    Route::delete('/messenger/{id}',[MessengerController::class,'destroy']);

});
