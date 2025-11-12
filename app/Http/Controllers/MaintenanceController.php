<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Maintenance;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\MaintenanceExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\EquipmentResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MaintenanceResource;
use Illuminate\Validation\ValidationException;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = Maintenance::with([ 'images','equipment', 'userBy', 'userTo', 'images_done'])->get();

        // Tambahkan log di sini
        foreach ($maintenances as $m) {
         Log::info('Maintenance info:', [
        'no_serial' => $m->no_serial,
              ]);
          }
        $data = MaintenanceResource::collection($maintenances);

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

public function store(Request $request)
{
    Log::info('🔹 Incoming Maintenance Request', $request->all());

    // Validasi input
    $validationRules = [
        'user_id_by' => 'required|exists:users,id',
        'name_mesin' => 'required|string',
        'jenis_perbaikan' => 'required|string',
        'keterangan' => 'nullable|string',
        'status_perbaikan' => 'required|integer|in:0,1,2',
        'equipment_id' => 'nullable|exists:equipment,id', 
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
        'name_mesin',
        'jenis_perbaikan',
        'keterangan',
        'status_perbaikan',
        'equipment_id',
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
        $maintenance = Maintenance::create($validated);

        Log::info('✅ Maintenance created successfully', [
            'id' => $maintenance->id,
            'data' => $validated,
        ]);

        // 🔹 Simpan gambar kalau ada
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('maintenances_images', 'public');
                    $maintenance->images()->create(['image_path' => $path]);

                    Log::info('🖼️ Image uploaded', [
                        'maintenance_id' => $maintenance->id,
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
            'data' => new MaintenanceResource($maintenance->load(['images','equipment','images_done'])),
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
    public function show($id)
{
    $maintenance = Maintenance::with(['equipment', 'userBy', 'userTo','images', 'images_done'])->find($id);

    if (!$maintenance) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new MaintenanceResource($maintenance)
    ]);
}

    public function update(Request $request, string $id)
    {
        try {
            // Cari assignment berdasarkan ID
            $maintenance = Maintenance::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'name_mesin' => 'required|string',
                'jenis_perbaikan' => 'required|string',
                'keterangan' => 'nullable|string',
                'equipment_id' => 'nullable|exists:equipment,id',
            ]);

            if ($request->filled('date')) {
                $validated['date'] = Carbon::parse($request->date)
                    ->setTimeFromTimeString(now()->format('H:i:s'));
            } else {
                $validated['date'] = $quality->date ?? now();
            }

            $maintenance->update($validated);
            // Update assignment dengan data yang sudah divalidasi
            $maintenance->update($validated);

            return response()->json([
                'message' => 'Maintenance updated successfully',
                'data' => new MaintenanceResource($maintenance->load(['equipment']))
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
        $maintenance = Maintenance::find($id);
        if (!$maintenance) {
            return response()->json(['message' => 'Maintenance not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_perbaikan' => 'required|integer|in:0,1',
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
            $path = $image->store('maintenances_images_done', 'public');
            $maintenance->images_done()->create([
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
            $maintenance->comment_done = $validated['comment_done'];
        }

        // Update status + date_end otomatis
        if (isset($validated['status_perbaikan'])) {
            $maintenance->status_perbaikan = $validated['status_perbaikan'];
            if ($validated['status_perbaikan'] == 1) {
                $maintenance->date_end = $maintenance->date_end
                    ? Carbon::parse($maintenance->date_end)->setTimeFromTimeString(now()->format('H:i:s'))
                    : now();
            }
        }

        if (array_key_exists('user_id_to', $validated)) {
            $maintenance->user_id_to = $validated['user_id_to'];
        }

        $maintenance->save();

        return response()->json([
            'message' => 'Maintenance updated successfully',
            'data' => new MaintenanceResource($maintenance->load(['images', 'images_done'])),
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
        $maintenance = Maintenance::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => MaintenanceResource::collection($maintenance)
        ]);
    }
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete)'
        ]);
    }
    public function restore($id)
    {
        $maintenance = Maintenance::withTrashed()->findOrFail($id);
        $maintenance->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new MaintenanceResource($maintenance)
        ]);
    }
     public function forceDelete($id)
    {
        $maintenance = Maintenance::withTrashed()->findOrFail($id);
        $maintenance->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }
 public function export()
{
    $fileName = 'maintenance_export_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new MaintenanceExport, $fileName);
}
 public function equipment_list()
    {
        return EquipmentResource::collection(Equipment::all());
    }

}
