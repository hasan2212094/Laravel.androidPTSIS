<?php

namespace App\Http\Controllers;

use App\Exports\QualityExport;
use App\Models\Quality;
use Illuminate\Http\Request;
use App\Models\QualityViewer;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\QualityResource;
use Illuminate\Support\Facades\Storage;
use App\http\Resources\QualityViewerResource;

class QualityController extends Controller
{
    public function index()
    {
        return QualityResource::collection(Quality::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'project' => 'required',
            'no_wo' => 'required|exists:workorders,id',
            'description' => 'required',
            'responds' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'date' => 'required|date',
            'status' => 'integer',
        ]);
        $quality = Quality::create([
            'project' => $request->project,
            'no_wo' => $request->no_wo,
            'description' => $request->description,
            'responds' => $request->responds,
            'date' => $request->date,
            'status' => $request->status ?? 0,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('qualities', 'public');
                $quality->images()->create(['image_path' => $path]);
            }
        }

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new QualityResource($quality->load(['workorder', 'images'])),
        ], 201);
    }

    public function show(Quality $quality)
    {
        $quality->load('workorder'); // memuat relasi

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
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'status' => 'integer',
        ]);

        $data = $request->only(['project', 'no_wo', 'description', 'date', 'status']);

        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($quality->image) {
                Storage::disk('public')->delete($quality->image);
            }

            $data['image'] = $request->file('image')->store('qualities', 'public');
        }

        $quality->update($data);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => new QualityResource($quality)
        ]);
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
