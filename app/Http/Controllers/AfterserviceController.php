<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use App\Models\AfterService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\AfterserviceExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AfterserviceResource;
use Illuminate\Validation\ValidationException;

class AfterserviceController extends Controller
{
    //
    public function index()
    {$afterservices = AfterService::with([ 'images_Progress', 'userBy', 'userTo', 'imagesDone'])->get();
        $data = AfterserviceResource::collection($afterservices);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
    public function store(Request $request)
{
    Log::info('🔹 Incoming Afterservice Request', $request->except(['images']));

    $validationRules = [
        'user_id_by' => 'required|exists:users,id',
        'client' => 'required|string|max:255',
        'jenis_kendaraan' => 'required|string|max:255',
        'no_polisi' => 'required|string|max:255',
        'no_rangka' => 'required|string|max:255',
        'produk' => 'required|string|max:255',
        'waranti' => 'required|in:0,1,true,false',
        'keterangan' => 'nullable|string|max:100000',
        'status_pekerjaan' => 'required|integer|in:0,1,2',
    ];

    $validator = Validator::make($request->all(), $validationRules);

    if ($validator->fails()) {
        Log::error('❌ Validation failed', $validator->errors()->toArray());
        return response()->json(['error' => $validator->errors()], 422);
    }

    $validated = $validator->validated();

    // ✅ CAST boolean
    $validated['waranti'] = filter_var($validated['waranti'], FILTER_VALIDATE_BOOLEAN);

    // ✅ DATE START OTOMATIS (SERVER TIME)
    $validated['date_start'] = now();

    try {
        $afterservice = AfterService::create($validated);

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new AfterserviceResource(
                $afterservice->load(['userBy','userTo'])
            ),
        ], 201);

    } catch (\Exception $e) {
        Log::error('💥 Error saat menyimpan afterservice', [
            'exception' => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'Terjadi kesalahan saat menyimpan data',
            'detail' => $e->getMessage(),
        ], 500);
    }
}
 public function update_progress(Request $request, string $id)
{
    try {
        $afterservice = AfterService::find($id);
        if (!$afterservice) {
            return response()->json(['message' => 'Afterservice not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_pekerjaan' => 'required|integer|in:0,1',
            'images_Progress' => 'nullable|array',
            'images_Progress.*' => 'image|mimes:jpg,jpeg,png|max:20240',
            'comment_progress' => 'sometimes|required|string|max:255',
        ]);

        /* =============================
         | Upload Images Progress
         ============================= */
       if ($request->hasFile('images_Progress.0')) {
            foreach ($request->images_Progress as $image) {
        if ($image->isValid()) {
            $path = $image->store(
                'afterservice_images_progress',
                'public'
            );
            $afterservice->images_Progress()->create([
                'image_path' => $path,
            ]);
        }
    }
}

        /* =============================
         | Update Fields
         ============================= */
        if (isset($validated['comment_progress'])) {
            $afterservice->comment_progress = $validated['comment_progress'];
        }

        if (isset($validated['user_id_to'])) {
            $afterservice->user_id_to = $validated['user_id_to'];
        }

        if (isset($validated['status_pekerjaan'])) {
            $afterservice->status_pekerjaan = $validated['status_pekerjaan'];
            // ❌ date_end JANGAN di progress
        }

        $afterservice->save();

        return response()->json([
            'message' => 'Afterservice progress updated successfully',
            'data' => new AfterserviceResource(
                 $afterservice->load(['images_Progress', 'userBy', 'userTo'])
            ),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating Afterservice',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function update_done(Request $request, string $id)
{
    try {
        $afterservice = AfterService::find($id);
        if (!$afterservice) {
            return response()->json(['message' => 'Afterservice not found'], 404);
        }

        $validated = $request->validate([
            'user_id_to' => 'sometimes|exists:users,id',
            'status_pekerjaan' => 'required|integer|in:2',
            'imagesDone' => 'nullable|array',
            'imagesDone.*' => 'image|mimes:jpg,jpeg,png|max:20240',
            'comment_done' => 'sometimes|required|string|max:255',
        ]);

        /* =============================
         | Upload Images DONE
         ============================= */
        if ($request->hasFile('imagesDone.0')) {
            foreach ($request->imagesDone as $image) {
                if ($image->isValid()) {
                    $path = $image->store(
                        'afterservice_images_done',
                        'public'
                    );

                    $afterservice->imagesDone()->create([
                        'image_path' => $path,
                    ]);
                }
            }
        }

        /* =============================
         | Update Fields
         ============================= */
        if (isset($validated['comment_done'])) {
            $afterservice->comment_done = $validated['comment_done'];
        }

        $afterservice->status_pekerjaan = 2;
        $afterservice->date_end = now();

        if (isset($validated['user_id_to'])) {
            $afterservice->user_id_to = $validated['user_id_to'];
        }

        $afterservice->save();

        return response()->json([
            'message' => 'Afterservice marked as DONE',
            'data' => new AfterserviceResource(
                $afterservice->load(['imagesDone', 'userBy', 'userTo'])
            ),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan pada server!',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function show(string $id)
   {
    $afterservice = AfterService::with([ 'userBy', 'userTo', 'images_Progress', 'imagesDone'])->find($id);

    if (!$afterservice) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new AfterserviceResource($afterservice)
    ]);
   }
public function update(Request $request, string $id)
{
    try {
        // Cari afterservice
        $afterservice = AfterService::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'client' => 'required|string|max:255',
            'jenis_kendaraan' => 'required|string|max:255',
            'no_polisi' => 'required|string|max:255',
            'no_rangka' => 'required|string|max:255',
            'produk' => 'required|string|max:255',
            'waranti' => 'required|in:0,1,true,false',
            'keterangan' => 'nullable|string|max:100000',
            'date' => 'nullable|date',
        ]);

        /* =============================
         | Handle Date
         ============================= */
        if ($request->filled('date')) {
            $validated['date'] = Carbon::parse($request->date)
                ->setTimeFromTimeString(
                    $afterservice->date
                        ? Carbon::parse($afterservice->date)->format('H:i:s')
                        : now()->format('H:i:s')
                );
        } else {
            // Tetap gunakan tanggal lama
            $validated['date'] = $afterservice->date;
        }

        // Update data
        $afterservice->update($validated);

        return response()->json([
            'message' => 'Afterservice updated successfully',
            'data' => new AfterserviceResource(
                $afterservice->load(['userBy', 'userTo', 'images_Progress', 'imagesDone'])
            )
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
public function export()
{
    $fileName = 'afterservice_export_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new AfterserviceExport, $fileName);
}
public function indexdelete()
    {
        $afterservice = AfterService::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => AfterserviceResource::collection($afterservice)
        ]);
    }
   public function destroy($id)
{
    $afterservice = AfterService::findOrFail($id);
    $afterservice->delete();

    return response()->json([
        'message' => 'Data berhasil dihapus (soft delete)'
    ]);
}
    public function restore($id)
    {
        $afterservice = AfterService::withTrashed()->findOrFail($id);
        $afterservice->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new AfterserviceResource($afterservice->load(['userBy', 'userTo', 'images_Progress', 'imagesDone']))
        ]);
    }
     public function forceDelete($id)
    {
        $afterservice = AfterService::withTrashed()->findOrFail($id);
        $afterservice->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }
}
