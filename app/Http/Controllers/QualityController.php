<?php

namespace App\Http\Controllers;

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
use App\http\Resources\QualityViewerResource;

class QualityController extends Controller
{
    public function index()
    {
        $qualities = Quality::with(['workorder', 'images'])->get();

        // Tambahkan log di sini
        foreach ($qualities as $q) {
            Log::info('Workorder relation:', [
                'quality_id' => $q->id,
                'workorder' => $q->workorder,
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
        $validationRules = [
            'project' => 'required',
            'no_wo' => 'required|exists:workorders,id',
            'description' => 'required',
            'responds' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'integer',
        ]);
        $quality = Quality::create([
            'project' =>(int) $request->project,
            'no_wo' => (int)$request->no_wo,
            'description' => (int)$request->description,
            'responds' => $request->responds,
            'date' => $request->date,
            'status' => $request->status ?? 0,
        ]);

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

    public function update(Request $request, Quality $quality)
    {
        $request->validate([
            'project' => 'required',
            'no_wo' => 'required|exists:workorders,id',
            'description' => 'required',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'integer',
        ]);

        $data = $request->only(['project', 'no_wo', 'description', 'date', 'status']);
        if ($request->hasFile('images')) {
            // Hapus semua gambar lama kalau perlu
            foreach ($quality->images as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }

            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('quality_images', 'public');
                    $quality->images()->create(['image_path' => $path]);
                }
            }
        }

        $quality->update($data);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => new QualityResource($quality)
        ]);
    }

    public function updaterelevanstatus(Request $request, string $id)
    {
        try {
            $quality = Quality::find($id);
            if (!$quality) {
                return response()->json(['message' => 'Quality not found'], 404);
            }

            $validated = $request->validate([
                'status' => 'required|integer|in:0,1',
                'status_relevan' => 'sometimes|integer',
                'comment' => 'sometimes|required|string|max:255',
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

            if (isset($validated['comment'])) {
                $quality->comment = $validated['comment'];
            }

            // if (isset($validated['description_relevan'])) {
            //     $quality->description_relevan = $validated['description_relevan'];
            // }

            if ($validated['status'] == 1 && !$quality->date_end) {
                $quality->date_end = now();
            }

            if ($request->has('status')) {
                $quality->status = (int) $request->input('status');
            }

            $quality->status = $validated['status'];
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
    public function updatenotrelevanstatus(Request $request, string $id)
    {
        try {
            $quality = Quality::find($id);
            if (!$quality) {
                return response()->json(['message' => 'Quality not found'], 404);
            }

            $validated = $request->validate([
                'status' => 'required|integer|in:0,1',
                'status_relevan' => 'sometimes|integer|in:0,1',
                // 'comment' => 'sometimes|required|string|max:255',
                'imagesrelevan' => 'nullable|array',
                'imagesrelevan.*' => 'image|mimes:jpg,jpeg,png|max:2048',
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

            if ($request->status == 1 && !$quality->date_end) {
                $quality->date_end = now();
            }

            if (isset($validated['status'])) {
                $quality->status = $validated['status'];
            }
            if (isset($validated['status_relevan'])) {
                $quality->status_relevan = $validated['status_relevan'];
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

    public function storeViewer(Request $request, $qualityId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $existingViewer = QualityViewer::where('quality_id', $qualityId)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingViewer) {
            return response()->json([
                'data' => new QualityViewerResource($existingViewer)
            ], 201);
        }


        Quality::where('id', $qualityId)->update(['responds' => true]);

        $viewer = QualityViewer::create([
            'quality_id' => $qualityId,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'message' => 'Data viewer berhasil disimpan',
            'data' => new QualityViewerResource($viewer)
        ], 201);
    }
    public function exportSummary()
    {
        return Excel::download(new QualityExport, 'qualities.xlsx');
    }
}
