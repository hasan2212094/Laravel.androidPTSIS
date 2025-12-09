<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Fabrikasi;
use App\Models\Workorder;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\FabrikasiExport;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UnitResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\FabrikasiResource;
use App\Http\Resources\WorkOrderResource;
use Illuminate\Support\Facades\Validator;
use FabrikasiExport as GlobalFabrikasiExport;
use Illuminate\Validation\ValidationException;

class FabrikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $fabrikasi = Fabrikasi::with(['workorder', 'userBy', 'userTo','unit'])->get();
    return response()->json([
        'status' => true,
        'data' => FabrikasiResource::collection($fabrikasi),
    ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       Log::info('🔹 Incoming Fabrikasi Request', $request->all());

    // Validasi input
    $validationRules = [
        'user_id_by' => 'required|exists:users,id',
        'jenis_Pekerjaan' => 'required|string',
        'keterangan' => 'nullable|string',
        'qty' => 'required|numeric',
        'unit_id' => 'required|exists:units,id',
        'status_pekerjaan' => 'required|integer|in:0,1,2',
        'workorder_id' => 'required|exists:workorders,id',
        'date_start' => 'nullable|date',
    ];

    $validator = Validator::make($request->all(), $validationRules);

    if ($validator->fails()) {
        Log::error('❌ Validation failed', [
            'errors' => $validator->messages()->toArray(),
        ]);

        return response()->json(['error' => $validator->messages()], 422);
    }

    $validated = Arr::only($validator->validated(), [
        'user_id_by',
        'jenis_Pekerjaan',
        'keterangan',
        'qty',
        'unit_id',
        'status_pekerjaan',
        'workorder_id',
    ]);
    $validated['qty'] = (int) $validated['qty'];
    // ✅ Format tanggal otomatis
    if ($request->filled('date_start')) {
    // Gunakan waktu saat ini (jam dari server)
    $validated['date_start'] = Carbon::parse($request->date_start . ' ' . now()->format('H:i:s'));
} else {
    $validated['date_start'] = now();
}


    // 🔹 Logging hasil parsing tanggal
    Log::info('✅ Parsed date values', [
        'date_start' => $validated['date_start'],
    ]);

    try {
        // Simpan data utama
        $fabrikasi = Fabrikasi::create($validated);

        Log::info('✅ Fabrikasi created successfully', [
            'id' => $fabrikasi->id,
            'data' => $validated,
        ]);


        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new FabrikasiResource($fabrikasi->load(['workorder', 'userBy', 'userTo','unit'])),
        ], 201);

    } catch (\Exception $e) {
        Log::error('💥 Error saat menyimpan maintenan', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Terjadi kesalahan saat menyimpan data',
            'detail' => $e->getMessage(),
        ], 500);
    }
    }

    public function workorder_list()
    {
        return WorkOrderResource::collection(Workorder::all());
    }
     public function unit_list()
    {
        return UnitResource::collection(Unit::all());
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
      $fabrikasi = Fabrikasi::with(['workorder', 'userBy', 'userTo','unit'])->find($id);

    if (!$fabrikasi) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new FabrikasiResource($fabrikasi)
    ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
{
    try {
        $fabrikasi = Fabrikasi::findOrFail($id);

        $validated = $request->validate([
            'jenis_Pekerjaan' => 'required|string',
            'keterangan' => 'nullable|string',
            'qty' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'workorder_id' => 'required|exists:workorders,id',
            'date' => 'nullable|date'
        ]);

        // 🔥 JANGAN ubah tanggal jika user tidak mengirimkan tanggal
        if (!$request->filled('date')) {
            unset($validated['date']);   // ← hapus agar tidak ikut diupdate
        } else {
            // Jika user memang mengubah tanggal, maka proses
            $validated['date'] = Carbon::parse($request->date);
        }

        // 🔥 Update satu kali saja
        $fabrikasi->update($validated);

        return response()->json([
            'message' => 'Fabrikasi updated successfully',
            'data' => new FabrikasiResource($fabrikasi->load(['workorder']))
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {        
        return response()->json([
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function updatedone(Request $request, string $id)
   {
    try {
        $fabrikasi = Fabrikasi::find($id);
        if (!$fabrikasi) {
            return response()->json(['message' => 'Maintenance not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_pekerjaan' => 'required|integer|in:0,1',
            'comment_done' => 'sometimes|required|string|max:255',
        ]);
        // Update comment
        if (isset($validated['comment_done'])) {
            $fabrikasi->comment_done = $validated['comment_done'];
        }

        // Update status + date_end otomatis
        if (isset($validated['status_pekerjaan'])) {
            $fabrikasi->status_pekerjaan = $validated['status_pekerjaan'];
            if ($validated['status_pekerjaan'] == 1) {
                $fabrikasi->date_end = $fabrikasi->date_end
                    ? Carbon::parse($fabrikasi->date_end)->setTimeFromTimeString(now()->format('H:i:s'))
                    : now();
            }
        }

        if (array_key_exists('user_id_to', $validated)) {
            $fabrikasi->user_id_to = $validated['user_id_to'];
        }

        $fabrikasi->save();

        return response()->json([
            'message' => 'Painting updated successfully',
            'data' => new FabrikasiResource($fabrikasi->load(['workorder'])),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating maintenance',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
     public function indexdelete()
    {
        $fabrikasi = Fabrikasi::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => FabrikasiResource::collection($fabrikasi)
        ]);
    }
    public function destroy(Fabrikasi $fabrikasi)
    {
        $fabrikasi->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete)'
        ]);
    }
    public function restore($id)
    {
        $fabrikasi = Fabrikasi::withTrashed()->findOrFail($id);
        $fabrikasi->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new FabrikasiResource($fabrikasi)
        ]);
    }
     public function forceDelete($id)
    {
        $fabrikasi = Fabrikasi::withTrashed()->findOrFail($id);
        $fabrikasi->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }
    public function export()
    {
        $fileName = 'fabrikasi_export_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new FabrikasiExport, $fileName);
    }
}
