<?php

namespace App\Http\Controllers;

use App\Exports\PaintingExport;
use App\Models\Painting;
use App\Models\Workorder;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\PaintingResource;
use App\Http\Resources\WorkOrderResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaintingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $painting = Painting::with(['workorder', 'userBy', 'userTo'])->get();
    return response()->json([
        'status' => true,
        'data' => PaintingResource::collection($painting),
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
        'qty'=>'required|string',
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
        'status_pekerjaan',
        'workorder_id',
    ]);

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
        $painting = Painting::create($validated);

        Log::info('✅ Painting created successfully', [
            'id' => $painting->id,
            'data' => $validated,
        ]);


        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new PaintingResource($painting->load(['workorder', 'userBy', 'userTo'])),
        ], 201);

    } catch (\Exception $e) {
        Log::error('💥 Error saat menyimpan maintenance', [
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
     {
      $painting = Painting::with(['workorder', 'userBy', 'userTo'])->find($id);

    if (!$painting) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new PaintingResource($painting)
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
           
            $painting = Painting::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                 'jenis_Pekerjaan' => 'required|string',
                 'keterangan' => 'nullable|string',
                 'qty'=>'required|string',
                 'workorder_id' => 'required|exists:workorders,id',
                
            ]);

            if ($request->filled('date_start')) {
                $validated['date_start'] = Carbon::parse($request->date)
                    ->setTimeFromTimeString(now()->format('H:i:s'));
            } else {
                $validated['date_start'] = $quality->date ?? now();
            }

            $painting->update($validated);
            // Update assignment dengan data yang sudah divalidasi
            $painting->update($validated);

            return response()->json([
                'message' => 'Painting updated successfully',
                'data' => new PaintingResource($painting->load(['workorder']))
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
        $painting = Painting::find($id);
        if (!$painting) {
            return response()->json(['message' => 'Maintenance not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_pekerjaan' => 'required|integer|in:0,1',
            'comment_done' => 'sometimes|required|string|max:255',
        ]);
        // Update comment
        if (isset($validated['comment_done'])) {
            $painting->comment_done = $validated['comment_done'];
        }

        // Update status + date_end otomatis
        if (isset($validated['status_pekerjaan'])) {
            $painting->status_pekerjaan = $validated['status_pekerjaan'];
            if ($validated['status_pekerjaan'] == 1) {
                $painting->date_end = $painting->date_end
                    ? Carbon::parse($painting->date_end)->setTimeFromTimeString(now()->format('H:i:s'))
                    : now();
            }
        }

        if (array_key_exists('user_id_to', $validated)) {
            $painting->user_id_to = $validated['user_id_to'];
        }

        $painting->save();

        return response()->json([
            'message' => 'Painting updated successfully',
            'data' => new PaintingResource($painting->load(['workorder'])),
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
        $painting = Painting::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => PaintingResource::collection($painting)
        ]);
    }
    public function destroy(Painting $painting)
    {
        $painting->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete)'
        ]);
    }
    public function restore($id)
    {
        $painting = Painting::withTrashed()->findOrFail($id);
        $painting->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new PaintingResource($painting)
        ]);
    }
     public function forceDelete($id)
    {
        $painting = Painting::withTrashed()->findOrFail($id);
        $painting->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }
    public function export()
    {
        $fileName = 'fabrikasi_export_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PaintingExport, $fileName);
    }
}
