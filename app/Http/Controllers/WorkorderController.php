<?php

namespace App\Http\Controllers;

use App\Models\Workorder;
use Illuminate\Http\Request;

class WorkorderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workorders = Workorder::all();
        return response()->json($workorders);
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
         $validated = $request->validate([
            'nomor' => 'required|string|max:255',
            'client' => 'nullable|string|max:255',
        ]);

        $workorder = Workorder::create($validated);

        return response()->json([
            'message' => 'Workorder created successfully',
            'data' => $workorder
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workorder = Workorder::findOrFail($id);
        return response()->json($workorder);
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
         $validated = $request->validate([
            'nomor' => 'sometimes|required|string|max:255',
            'client' => 'nullable|string|max:255',
        ]);

        $workorder = Workorder::findOrFail($id);
        $workorder->update($validated);

        return response()->json([
            'message' => 'Workorder updated successfully',
            'data' => $workorder
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
     $workorder = Workorder::findOrFail($id);

    // Jika punya relasi
    $workorder->qualities()->delete();
    $workorder->paintings()->delete();
    $workorder->fabrikasis()->delete();
    $workorder->komponens()->delete();
    $workorder->electricals()->delete();

    $workorder->delete();

    return response()->json(['message' => 'Workorder deleted successfully']);
    }
}
