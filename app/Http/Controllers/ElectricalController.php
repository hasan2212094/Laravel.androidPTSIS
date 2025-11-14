<?php

namespace App\Http\Controllers;

use App\Models\Workorder;
use App\Models\Electrical;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ElectricalExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\WorkOrderResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ElectricalResource;
use Illuminate\Validation\ValidationException;

class ElectricalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {$electricals = Electrical::with([ 'images','workorder', 'userBy', 'userTo', 'images_done'])->get();
        $data = ElectricalResource::collection($electricals);

        return response()->json([
            'status' => true,
            'data' => $data,
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
    Log::info('🔹 Incoming Electrical Request', $request->all());

    // Validasi input
    $validationRules = [
        'user_id_by' => 'required|exists:users,id',
        'jenis_Pekerjaan' => 'required|string',
        'keterangan' => 'nullable|string',
        'qty'=>'required|string',
        'status_pekerjaan' => 'required|integer|in:0,1,2',
        'workorder_id' => 'required|exists:workorders,id', 
        'date_start' => 'nullable|date',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:20240',
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
        $electrical = Electrical::create($validated);

        Log::info('✅ Electrical created successfully', [
            'id' => $electrical->id,
            'data' => $validated,
        ]);

        // 🔹 Simpan gambar kalau ada
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('electrical_images', 'public');
                    $electrical->images()->create(['image_path' => $path]);

                    Log::info('🖼️ Image uploaded', [
                        'electrical_id' => $electrical->id,
                        'path' => $path,
                    ]);
                } else {
                    Log::warning('⚠️ Image invalid', [
                        'file' => $image->getClientOriginalName(),
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new ElectricalResource($electrical->load(['images','workorder','images_done'])),
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
   {
    $electrical = Electrical::with(['workorder', 'userBy', 'userTo','images', 'images_done'])->find($id);

    if (!$electrical) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new ElectricalResource($electrical)
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
            // Cari assignment berdasarkan ID
            $electrical = Electrical::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'jenis_Pekerjaan' => 'required|string',
                'keterangan' => 'nullable|string',
                'qty'=>'required|string',
                'workorder_id' => 'required|exists:workorders,id',
            ]);

            if ($request->filled('date')) {
                $validated['date'] = Carbon::parse($request->date)
                    ->setTimeFromTimeString(now()->format('H:i:s'));
            } else {
                $validated['date'] = $electrical->date ?? now();
            }

            $electrical->update($validated);
            // Update assignment dengan data yang sudah divalidasi
            $electrical->update($validated);

            return response()->json([
                'message' => 'Electrical updated successfully',
                'data' => new ElectricalResource($electrical->load(['workorder']))
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
        $electrical = Electrical::find($id);
        if (!$electrical) {
            return response()->json(['message' => 'Electrical not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_pekerjaan' => 'required|integer|in:0,1',
            'images_done' => 'nullable|array',
            'images_done.*' => 'image|mimes:jpg,jpeg,png|max:20240',
            'comment_done' => 'sometimes|required|string|max:255',
        ]);

        // Upload gambar done
        $files = $request->file('images_done');
if ($files) {
    // pastikan selalu array
    $files = is_array($files) ? $files : [$files];

    foreach ($files as $image) {
        if ($image->isValid()) {
            $path = $image->store('electrical_images_done', 'public');
            $electrical->images_done()->create([
                'image_path_done' => $path,
            ]);
        } else {
            Log::warning('⚠️ Invalid image upload', [
                'file' => $image->getClientOriginalName(),
            ]);
        }
    }
}

        // Update comment
        if (isset($validated['comment_done'])) {
            $electrical->comment_done = $validated['comment_done'];
        }

        // Update status + date_end otomatis
        if (isset($validated['status_pekerjaan'])) {
            $electrical->status_pekerjaan = $validated['status_pekerjaan'];
            if ($validated['status_pekerjaan'] == 1) {
                $electrical->date_end = $electrical->date_end
                    ? Carbon::parse($electrical->date_end)->setTimeFromTimeString(now()->format('H:i:s'))
                    : now();
            }
        }

        if (array_key_exists('user_id_to', $validated)) {
            $electrical->user_id_to = $validated['user_id_to'];
        }

        $electrical->save();

        return response()->json([
            'message' => 'Electrical updated successfully',
            'data' => new ElectricalResource($electrical->load(['images', 'images_done'])),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating electrical',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
  public function indexdelete()
    {
        $electrical = Electrical::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => ElectricalResource::collection($electrical)
        ]);
    }
    public function destroy(Electrical $electrical)
    {
        $electrical->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete)'
        ]);
    }
    public function restore($id)
    {
        $electrical = Electrical::withTrashed()->findOrFail($id);
        $electrical->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new ElectricalResource($electrical)
        ]);
    }
     public function forceDelete($id)
    {
        $electrical = Electrical::withTrashed()->findOrFail($id);
        $electrical->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }
 public function export()
{
    $fileName = 'electrical_export_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new ElectricalExport, $fileName);
}
 public function workorder_list()
    {
        return WorkOrderResource::collection(Workorder::all());
    }

}
