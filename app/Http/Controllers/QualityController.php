<?php

namespace App\Http\Controllers;

use App\Models\Quality;
use Illuminate\Http\Request;
use App\Http\Resources\QualityResource;
use Illuminate\Support\Facades\Storage;

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
            'no_wo' => 'required',
            'description' => 'required',
            'responds' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'date' => 'required|date',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $quality = Quality::create([
            'project' => $request->project,
            'no_wo' => $request->no_wo,
            'description' => $request->description,
            'responds' => $request->responds,
            'image' => $imagePath,
            'date' => $request->date,
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new QualityResource($quality)
        ], 201);
    }

    public function show(Quality $quality)
    {
        return new QualityResource($quality);
    }

    public function update(Request $request, Quality $quality)
    {
        $request->validate([
            'project' => 'required',
            'no_wo' => 'required',
            'description' => 'required',
            'responds' => 'required|boolean',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $data = $request->only(['project', 'no_wo', 'description', 'responds', 'date']);

        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($quality->image) {
                Storage::disk('public')->delete($quality->image);
            }

            $data['image'] = $request->file('image')->store('images', 'public');
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
}
