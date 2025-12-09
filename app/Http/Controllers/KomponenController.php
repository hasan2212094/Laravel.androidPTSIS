<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Komponen;
use App\Models\Workorder;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\KomponenExport;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UnitResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\KomponenResource;
use App\Http\Resources\WorkOrderResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KomponenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $komponen = Komponen::with(['workorder', 'userBy', 'userTo','unit'])->get();
    return response()->json([
        'status' => true,
        'data' => KomponenResource::collection($komponen),
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
        Log::info('🔹 Incoming Komponen Request', $request->all());

    // Validasi input
    $validationRules = [
        'user_id_by' => 'required|exists:users,id',
        'jenis_Pekerjaan' => 'required|string',
        'keterangan' => 'nullable|string',
        'spekifikasi' => 'nullable|string',
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
        'spekifikasi',
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
        $komponen  = Komponen::create($validated);

        Log::info('✅ data created successfully', [
            'id' => $komponen->id,
            'data' => $validated,
        ]);


        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new KomponenResource($komponen->load(['workorder', 'userBy', 'userTo','unit'])),
        ], 201);

    } catch (\Exception $e) {
        Log::error('💥 Error saat menyimpan komponen', [
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
    public function show(string $id)
    {
          $komponen = Komponen::with(['workorder', 'userBy', 'userTo','unit'])->find($id);

    if (!$komponen) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new KomponenResource($komponen)
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

        $komponen = Komponen::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'jenis_Pekerjaan' => 'required|string',
            'keterangan' => 'nullable|string',
            'qty' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'workorder_id' => 'required|exists:workorders,id',
        ]);

        // Update tanggal hanya jika dikirim oleh user
        if ($request->filled('date')) {
            $validated['date'] = Carbon::parse($request->date)
                ->setTimeFromTimeString(now()->format('H:i:s'));
        } else {
            // tetap gunakan tanggal lama
            $validated['date'] = $komponen->date;
        }

        // Update data
        $komponen->update($validated);

        return response()->json([
            'message' => 'Komponen updated successfully',
            'data' => new KomponenResource($komponen->load(['workorder']))
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
        $komponen = Komponen::find($id);
        if (!$komponen) {
            return response()->json(['message' => 'Maintenance not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_pekerjaan' => 'required|integer|in:0,1',
            'comment_done' => 'sometimes|required|string|max:255',
        ]);
        // Update comment
        if (isset($validated['comment_done'])) {
            $komponen->comment_done = $validated['comment_done'];
        }

        // Update status + date_end otomatis
        if (isset($validated['status_pekerjaan'])) {
            $komponen->status_pekerjaan = $validated['status_pekerjaan'];
            if ($validated['status_pekerjaan'] == 1) {
                $komponen->date_end = $komponen->date_end
                    ? Carbon::parse($komponen->date_end)->setTimeFromTimeString(now()->format('H:i:s'))
                    : now();
            }
        }

        if (array_key_exists('user_id_to', $validated)) {
            $komponen->user_id_to = $validated['user_id_to'];
        }

        $komponen->save();

        return response()->json([
            'message' => 'Maintenance updated successfully',
            'data' => new KomponenResource($komponen->load(['workorder'])),
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
        $komponen = Komponen::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => KomponenResource::collection($komponen)
        ]);
    }
    public function destroy(Komponen $komponen)
    {
        $komponen->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete)'
        ]);
    }
    public function restore($id)
    {
        $komponen = Komponen::withTrashed()->findOrFail($id);
        $komponen->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new KomponenResource($komponen)
        ]);
    }
     public function forceDelete($id)
    {
        $komponen = Komponen::withTrashed()->findOrFail($id);
        $komponen->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }
    public function export()
    {
        $fileName = 'komponen' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new KomponenExport, $fileName);
    }
}
