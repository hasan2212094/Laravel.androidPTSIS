<?php

namespace App\Http\Controllers;

use App\Models\Messenger;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\MessengerResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MessengerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
        $messages = Messenger::with('userBy')->get();

        return response()->json([
            'status' => true,
            'data' => MessengerResource::collection($messages),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan pada server',
            'error' => $e->getMessage(),
        ], 500);
    }
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
        Log::info('🔹 Incoming Messenger Request', $request->all());

    // Validasi input
    $validationRules = [
        'user_id_by' => 'required|exists:users,id',
        'title' => 'required|string',
        'message' => 'required|string'
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
        'title',
        'message'
    ]);

    try {
        // Simpan data utama
        $messenge  = Messenger::create($validated);

        Log::info('✅ Messenger created successfully', [
            'id' => $messenge->id,
            'data' => $validated,
        ]);


        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => new MessengerResource($messenge->load([ 'userBy', 'userTo'])),
        ], 201);

    } catch (\Exception $e) {
        Log::error('💥 Error saat menyimpan messenge', [
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
        $messenger = Messenger::with([ 'userBy', 'userTo'])->find($id);

    if (!$messenger) {
        return response()->json([
            'message' => 'Data tidak ditemukan',
            'data' => null
        ], 404);
    }

    return response()->json([
        'message' => 'Data ditemukan',
        'data' => new MessengerResource($messenger)
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
            $messenger = Messenger::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'title' => 'required|string',
                'message' => 'nullable|string'
            ]);

            $messenger->update($validated);

            return response()->json([
                'message' => 'Maintenance updated successfully',
                'data' => new MessengerResource($messenger)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $messenger = Messenger::find($id);

        if (!$messenger) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $messenger->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }
}
