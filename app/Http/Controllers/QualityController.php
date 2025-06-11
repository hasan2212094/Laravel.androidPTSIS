<?php

namespace App\Http\Controllers;

use App\Models\Quality;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index()
    {
        return response()->json(Quality::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'project' => 'required',
            'no_wo' => 'required',
            'description' => 'required',
            'responds' => 'required|boolean', // sudah boolean, bukan foreign key lagi
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
            'responds' => $request->responds, // true/false
            'image' => $imagePath,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Data berhasil disimpan', 'data' => $quality], 201);
    }

    public function show(Quality $quality)
    {
        if (!$quality) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $quality
        ]);
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

        $data = $request->all();

        // Optional: handle update image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $quality->update($data);

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => $quality
        ], 200);
    }

    public function destroy(Quality $quality)
    {
        $quality->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ], 200);
    }
}
