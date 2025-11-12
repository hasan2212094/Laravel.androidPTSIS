<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Quality;
use App\Models\Workorder;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\QualityViewer;
use App\Exports\QualityExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\QualityResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\WorkOrderResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\QualityViewerResource;
use Illuminate\Validation\ValidationException;

class QualityController extends Controller
{
    public function index()
    {
        $qualities = Quality::with(['workorder', 'images'])->get();

        // Tambahkan log di sini
        foreach ($qualities as $q) {
            Log::info('Workorder relation:', [
                'quality_id' => $q->id,
                'workorder' => $q->workorder ? $q->workorder->nomor : null,
            ]);
        }

        $data = QualityResource::collection($qualities);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function workorder_list()
    {
        return WorkOrderResource::collection(Workorder::all());
    }


    public function store(Request $request)
    {
        $validator = $validationRules = [
            'user_id_by' => 'required|exists:users,id',
            'project' => 'required',
            'workorder_id' => 'required|exists:workorders,id',
            'description' => 'required',
            'responds' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:20240',
            'status' => 'integer',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        $validated = Arr::only($validator->validated(), [
            'user_id_by',
            'project',
            'workorder_id',
            'description',
            'responds',
            'status'
        ]);
        if ($request->filled('date')) {
            // Jika request mengirim field `date`, paksa ada jam
            $validated['date'] = Carbon::parse($request->date)
                ->setTimeFromTimeString(now()->format('H:i:s'));
        } else {
            // Jika tidak ada, set ke jam saat ini
            $validated['date'] = now();
        }

        $quality = Quality::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('quality_images', 'public');
                    $quality->images()->create(['image_path' => $path]);
                }
            }
        }

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new QualityResource($quality->load(['workorder', 'images'])),
        ], 201);
    }
    public function show(Quality $quality)
    {
        $quality->load(['workorder', 'images']);

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => new QualityResource($quality),
        ]);
    }

    public function update(Request $request,  string $id)
    {
        // try {
        //     $validated = $request->validate([
        //         'user_id_by' => 'required|exists:users,id',
        //         'role_by' => 'required|exists:roles,id',
        //         'user_id_to' => 'required|exists:users,id',
        //         'role_to' => 'required|exists:roles,id',
        //         'project' => 'required|string|max:255',
        //         'no_wo' => 'required|exists:workorders,id',
        //         'description' => 'required|string',
        //         // 'responds' => 'sometimes|boolean',
        //         // 'images' => 'nullable|array',
        //         // 'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        //         // 'status' => 'integer|in:0,1',
        //         'date' => 'nullable|date',
        //     ]);

        //     // Tambahkan status_relevan = 0 secara otomatis
        //     $validated['date'] = now(); // otomatis isi tanggal saat update

        //     // Update data
        //     $quality->update($validated);

        //     // Handle gambar jika ada
        //     // if ($request->hasFile('images')) {
        //     //     // Hapus gambar lama
        //     //     foreach ($quality->images as $img) {
        //     //         Storage::disk('public')->delete($img->image_path);
        //     //         $img->delete();
        //     //     }

        //     //     // Simpan gambar baru
        //     //     foreach ($request->file('images') as $image) {
        //     //         if ($image->isValid()) {
        //     //             $path = $image->store('quality_images', 'public');
        //     //             $quality->images()->create(['image_path' => $path]);
        //     //         }
        //     //     }
        //     // }

        //     return response()->json([
        //         'message' => 'Data berhasil diperbarui',
        //         'data' => new QualityResource($quality->load('images'))
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'Data gagal diperbarui',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
        try {
            // Cari assignment berdasarkan ID
            $quality = Quality::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'user_id_by' => 'sometimes|exists:users,id',
                'project' => 'required|string|max:255',
                'workorder_id' => 'required|exists:workorders,id',
                'description' => 'required|string',
            ]);

            if ($request->filled('date')) {
                $validated['date'] = Carbon::parse($request->date)
                    ->setTimeFromTimeString(now()->format('H:i:s'));
            } else {
                $validated['date'] = $quality->date ?? now();
            }

            $quality->update($validated);
            // Update assignment dengan data yang sudah divalidasi
            $quality->update($validated);

            return response()->json([
                'message' => 'Quality updated successfully',
                'data' => new QualityResource($quality->load(['workorder']))
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
    public function updatenotrelevanstatus(Request $request, string $id)
    {
        try {
            $quality = Quality::find($id);
            if (!$quality) {
                return response()->json(['message' => 'Quality not found'], 404);
            }

            $validated = $request->validate([
                'user_id_to' => 'sometimes|exists:users,id',
                // 'status' => 'required|integer|in:0,1',
                'status_relevan' => 'sometimes|integer',
                'comment' => 'nullable|string|max:255',
                // 'imagesrelevan' => 'nullable|array',
                // 'imagesrelevan.*' => 'image|mimes:jpg,jpeg,png|max:2048',
                // 'description_relevan' => 'sometimes|required|string|max:255',
            ]);

            // if ($request->hasFile('imagesrelevan')) {
            //     foreach ($request->file('imagesrelevan') as $image) {
            //         if ($image->isValid()) {
            //             $path = $image->store('quality_image_relevans', 'public');
            //             $quality->imagesrelevan()->create([
            //                 'image_path_relevan' => $path
            //             ]);
            //         }
            //     }
            // }

            if (array_key_exists('comment', $validated)) {
                $quality->comment = $validated['comment'];
            }

            // Update status_relevan jika dikirim
            if (array_key_exists('status_relevan', $validated)) {
                $quality->status_relevan = $validated['status_relevan'];
            }

            // if (isset($validated['description_relevan'])) {
            //     $quality->description_relevan = $validated['description_relevan'];
            // }

            // if ($validated['status'] == 1 && !$quality->date_end) {
            //     $quality->date_end = now();
            // }
            if (array_key_exists('user_id_to', $validated)) {
                $quality->user_id_to = $validated['user_id_to'];
            }



            // $quality->status = $validated['status'];
            // $quality->updated_at = now();
            $quality->save();

            return response()->json(['message' => 'Quality updated successfully', 'data' => new QualityResource($quality),], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating quality',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updaterelevanstatus(Request $request, string $id)
    {
        try {
            $quality = Quality::find($id);
            if (!$quality) {
                return response()->json(['message' => 'Quality not found'], 404);
            }

            $validated = $request->validate([
                'user_id_to' => 'sometimes|exists:users,id',
                // 'status' => 'required|integer|in:0,1',
                'status_relevan' => 'sometimes|integer|in:0,1',
                // 'comment' => 'sometimes|required|string|max:255',
                'imagesrelevan' => 'nullable|array',
                'imagesrelevan.*' => 'image|mimes:jpg,jpeg,png|max:20240',
                'description_relevan' => 'sometimes|required|string|max:255',
            ]);

            if ($request->hasFile('imagesrelevan')) {
                foreach ($request->file('imagesrelevan') as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('image_path_relevan', 'public');
                        $quality->imagesrelevan()->create([
                            'image_path_relevan' => $path
                        ]);
                    }
                }
            }

            // if (isset($validated['comment'])) {
            //     $quality->comment = $validated['comment'];
            // }

            if (isset($validated['description_relevan'])) {
                $quality->description_relevan = $validated['description_relevan'];
            }

            // if ($request->status == 1 && !$quality->date_end) {
            //     $quality->date_end = now();
            // }

            // if (isset($validated['status'])) {
            //     $quality->status = $validated['status'];
            // }
            if (isset($validated['status_relevan'])) {
                $quality->status_relevan = $validated['status_relevan'];
            }
            if (array_key_exists('user_id_to', $validated)) {
                $quality->user_id_to = $validated['user_id_to'];
            }


            // $quality->updated_at = now();
            $quality->save();

            return response()->json(['message' => 'Quality updated successfully', 'data' => new QualityResource($quality),], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating quality',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updatedone(Request $request, string $id)
    {
        try {
            $quality = Quality::find($id);
            if (!$quality) {
                return response()->json(['message' => 'Quality not found'], 404);
            }

            $validated = $request->validate([
                'status' => 'required|integer|in:0,1,2',
                'comment_done' => 'nullable|string|max:255',
            ]);
            if (array_key_exists('comment_done', $validated)) {
                $quality->comment_done = $validated['comment_done'];
            }

            if ($validated['status'] == 2 && !$quality->date_end) {
                $quality->date_end = now();
            }



            $quality->status = $validated['status'];
            $quality->updated_at = now();
            $quality->save();

            $quality->load(['workorder', 'images', 'imagesrelevan', 'imagesprogress', 'userBy', 'userTo']);

            return response()->json(['message' => 'Quality updated successfully', 'data' => new QualityResource($quality),], 200);
        } catch (\Exception $e) {
             Log::error('Error in updatedone: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating quality',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateinprogress(Request $request, string $id)
    {
        try {
            $quality = Quality::find($id);
            if (!$quality) {
                return response()->json(['message' => 'Quality not found'], 404);
            }

            $validated = $request->validate([
                'status' => 'required|integer|in:0,1,2',
                'imagesprogress' => 'nullable',
                'imagesprogress.*' => 'image|mimes:jpg,jpeg,png|max:20240',
                'description_progress' => 'sometimes|required|string|max:255',
            ]);

            if ($request->hasFile('imagesprogress')) {
                foreach ($request->file('imagesprogress') as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('image_path_inprogress', 'public');
                        $quality->imagesprogress()->create([
                            'image_path_inprogress' => $path
                        ]);
                    }
                }
            }


            if (isset($validated['description_progress'])) {
                $quality->description_progress = $validated['description_progress'];
            }

            if ($request->status == 1 && !$quality->date_end) {
                $quality->date_end = now();
            }

            if (isset($validated['status'])) {
                $quality->status = $validated['status'];
            }
            $quality->updated_at = now();
            $quality->save();

            return response()->json(['message' => 'Quality updated successfully', 'data' => new QualityResource($quality),], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating quality',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Quality $quality)
    {
        $quality->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete)'
        ]);
    }

    public function restore($id)
    {
        $quality = Quality::withTrashed()->findOrFail($id);
        $quality->restore();

        return response()->json([
            'message' => 'Data berhasil direstore',
            'data' => new QualityResource($quality)
        ]);
    }

    public function forceDelete($id)
    {
        $quality = Quality::withTrashed()->findOrFail($id);
        $quality->forceDelete();

        return response()->json(['message' => 'Data dihapus permanen.']);
    }

    public function indexdelete()
    {
        $qualities = Quality::withTrashed()->get();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => QualityResource::collection($qualities)
        ]);
    }

    public function showViewer($qualityId)
    {
        $viewers = QualityViewer::where('quality_id', $qualityId)->get();

        return response()->json([
            'message' => 'Data viewers retrieved successfully',
            'data' => QualityViewerResource::collection($viewers)
        ], 200);
    }

    // public function storeViewer(Request $request, $qualityId)
    // {
    //     // Validasi user_id
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //     ]);

    //     // Pastikan Quality dengan ID ini ada
    //     $quality = Quality::find($qualityId);
    //     if (!$quality) {
    //         return response()->json([
    //             'message' => 'Quality not found'
    //         ], 404);
    //     }

    //     // Cek apakah viewer sudah ada
    //     $existingViewer = QualityViewer::where('quality_id', $qualityId)
    //         ->where('user_id', $request->user_id)
    //         ->first();

    //     if ($existingViewer) {
    //         return response()->json([
    //             'message' => 'Viewer already exists',
    //             'data' => new QualityViewerResource($existingViewer)
    //         ], 200); // ubah ke 200 karena data sudah ada
    //     }

    //     // Update responds = true di Quality
    //     $quality->update(['responds' => true]);

    //     // Buat viewer baru
    //     $viewer = QualityViewer::create([
    //         'quality_id' => $qualityId,
    //         'user_id' => $request->user_id,
    //     ]);

    //     // Load relasi user untuk resource
    //     $viewer->load('user');

    //     return response()->json([
    //         'message' => 'Data viewer berhasil disimpan',
    //         'data' => new QualityViewerResource($viewer)
    //     ], 201);
    // }
    public function storeViewer(Request $request, $qualityId)
    {
        // Validasi user_id
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Cek apakah Quality dengan ID ini ada
        $quality = Quality::find($qualityId);
        if (!$quality) {
            return response()->json([
                'message' => 'Quality not found, no viewer added'
            ], 200); // return 200 bukan 404
        }

        // Cek apakah viewer sudah ada
        $existingViewer = QualityViewer::where('quality_id', $qualityId)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingViewer) {
            return response()->json([
                'message' => 'Viewer already exists',
                'data' => new QualityViewerResource($existingViewer)
            ], 200);
        }

        // Update responds = true di Quality
        $quality->update(['responds' => true]);

        // Buat viewer baru
        $viewer = QualityViewer::create([
            'quality_id' => $qualityId,
            'user_id' => $request->user_id,
        ]);

        // Load relasi user untuk resource
        $viewer->load('user');

        return response()->json([
            'message' => 'Data viewer berhasil disimpan',
            'data' => new QualityViewerResource($viewer)
        ], 200);
    }

    public function exportSummary(Request $request)
    {
        try {
            // Validasi input filter (opsional)
            $request->validate([
                'status' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $filters = [
                'status' => $request->input('status'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ];

            // Generate file name
            $fileName = 'quality_export_' . now()->format('Ymd_His') . '.xlsx';

            // Jalankan export Excel
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\QualityExport($filters),
                $fileName
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return kalau validasi gagal
            return response()->json([
                'message' => 'Validasi gagal!',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Return kalau ada error lain
            return response()->json([
                'message' => 'Terjadi kesalahan saat export!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
