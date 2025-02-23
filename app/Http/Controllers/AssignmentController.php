<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return AssignmentResource::collection(Assignment::with('user', 'role')->get());
    // }
    public function index(Request $request)
    {
        $bodyContent = $request->all();
        $role_by = $bodyContent['role_by'];
        $role_to = $bodyContent['role_to'];

        $assignments = Assignment::with('user', 'role')
            ->where(function ($query) use ($role_by, $role_to) {
                $query->where('role_by', $role_by)
                    ->orWhere('role_to', $role_to);
            })
            ->get();

        return AssignmentResource::collection($assignments);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $validationRules = [
            'user_id_by' => 'required|exists:users,id',
            'role_by' => 'required|exists:roles,id',
            'user_id_to' => 'required|exists:users,id',
            'role_to' => 'required|exists:roles,id',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'date_start' => 'required|date',
            'level_urgent' => 'boolean', // Validasi level_urgent harus true/false
            'status' => 'boolean', // Validasi level_urgent harus true/false
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $validatedData = $validator->validated();
        $onlyFields = Arr::only($validatedData, ['user_id_by', 'role_by', 'user_id_to', 'role_to', 'title', 'description', 'date_start', 'level_urgent']); // Masukkan data yang dipilih ke dalam tabel
        Assignment::create($onlyFields);

        // $path = null;
        // if ($request->hasFile('image')) {
        //     $path = $request->file('image')->store('assignments', 'public');
        // }
        // $assignment = Assignment::create([
        //     'user_id_by' => $request->user_id,
        //     'role_by' => $request->role_id,
        //     'user_id_to' => $request->user_id,
        //     'role_to' => $request->role_id,
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'date_start' => $request->date,
        //     'level_urgent' => $request->level_urgent ?? true, // Jika tidak diisi, default true
        //     'status' => $request->status ?? false, // Default false (belum selesai)
        // ]);

        return response()->json([
            'message' => 'Tugas berhasil ditambahkan!',
            'data' => $onlyFields
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assignment = Assignment::find($id);
        if (!$assignment) {
            return response()->json(['message' => 'Assignment not found'], 404);
        }
        return response()->json($assignment, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePembuat(Request $request, string $id)
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Tugas tidak ditemukan!'
            ], 404);
        }

        $assignment->update($request->all());

        return response()->json([
            'message' => 'Tugas berhasil diperbarui!',
            'data' => $assignment
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return response()->json([
                'message' => 'Tugas tidak ditemukan!'
            ], 404);
        }

        $assignment->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus!'
        ], 200);
    }
}
