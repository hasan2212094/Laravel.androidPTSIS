<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AssignmentResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Exports\AssignmentSummaryExport;
use Maatwebsite\Excel\Facades\Excel;


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
        $role_id = $request->query('role_id');

        $assignments = Assignment::with('user', 'role')
            ->where(function ($query) use ($role_id) {
                $query->where('role_by', $role_id)
                    ->orWhere('role_to', $role_id);
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
            'level_urgent' => 'boolean', // Validasi level_urgent harus true/false
            'status' => 'integer',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $validatedData = $validator->validated(); // Masukkan data yang dipilih ke dalam tabel
        $onlyFields = Arr::only($validatedData, ['user_id_by', 'role_by', 'user_id_to', 'role_to', 'title', 'description', 'level_urgent']);
        $validatedData['date_start'] = now();
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
    public function updateStatus(Request $request, string $id)
    {
        try {
            // Cek apakah assignment dengan ID tersebut ada
            $assignment = Assignment::find($id);
            if (!$assignment) {
                return response()->json(['message' => 'Assignment not found'], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'image' => 'nullable|image|mimetypes:image/*|max:2048',
                'finish_note' => 'sometimes|required|string|max:255',
                'status' => 'integer',
            ]);

            // Handle image upload jika ada file gambar yang dikirim
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($assignment->image) {
                    Storage::disk('public')->delete($assignment->image);
                }

                // Simpan gambar baru
                $assignment->image = $request->file('image')->store('assignments', 'public');
            }

            // Update data hanya jika diberikan di request
            if ($request->has('finish_note')) {
                $assignment->finish_note = $validated['finish_note'];
            }

            if ($request->status == 1 && !$assignment->date_end) {
                $assignment->date_end = now(); // Simpan waktu sekarang saat tugas selesai
            }

            if ($request->has('status')) {
                $assignment->status = $validated['status'];
            }


            $assignment->updated_at = now();

            // Simpan perubahan
            $assignment->save();

            return response()->json(['message' => 'Assignment updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating assignment', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assignment = Assignment::with('user', 'role')->find($id);

        if (!$assignment) {
            return response()->json(['message' => 'Assignment not found'], 404);
        }
        return new AssignmentResource($assignment);
        //return response()->json($assignment, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePembuat(Request $request, string $id)
    {
        try {
            // Cari assignment berdasarkan ID
            $assignment = Assignment::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'user_id_by' => 'required|exists:users,id',
                'role_by' => 'required|exists:roles,id',
                'user_id_to' => 'required|exists:users,id',
                'role_to' => 'required|exists:roles,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'level_urgent' => 'boolean',
                'status' => 'integer',
            ]);

            $validated['date_start'] = now();

            $assignment->update($validated);
            // Update assignment dengan data yang sudah divalidasi
            $assignment->update($validated);

            return response()->json([
                'message' => 'Assignment updated successfully',
                'data' => $assignment
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
    // public function updateEnd(Request $request, string $id)
    // {
    //     try {
    //         // Cari assignment berdasarkan ID, jika tidak ditemukan akan otomatis melempar error
    //         $assignment = Assignment::findOrFail($id);

    //         // Validasi input
    //         $validated = $request->validate([
    //             'image' => 'nullable|image|mimetypes:image/*|max:2048',
    //             'finish_note' => 'sometimes|required|string|max:255',
    //             'date_end' => 'sometimes|required|date',
    //             'status' => 'boolean',
    //         ]);

    //         // Handle upload gambar jika ada
    //         if ($request->hasFile('image')) {
    //             // Hapus gambar lama jika ada
    //             if (!empty($assignment->image)) {
    //                 Storage::disk('public')->delete($assignment->image);
    //             }

    //             // Simpan gambar baru dan update path ke database
    //             $validated['image'] = $request->file('image')->store('assignments', 'public');
    //         }

    //         // Pastikan hanya mengupdate jika ada perubahan data
    //         if (!empty($validated)) {
    //             $assignment->update($validated);
    //         }

    //         return response()->json([
    //             'message' => 'Assignment updated successfully',
    //             'data' => [
    //                 'id' => $assignment->id,
    //                 'finish_note' => $assignment->finish_note,
    //                 'date_end' => $assignment->date_end,
    //                 'status' => $assignment->status,
    //                 'image' => $assignment->image ? asset('storage/' . $assignment->image) : null,
    //             ]
    //         ], 200);
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'message' => 'Validation error',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'Assignment not found',
    //             'error' => $e->getMessage()
    //         ], 404);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Internal server error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function destroy(string $id)
    {
        try {
            $assignment = Assignment::findOrFail($id);
            $assignment->delete(); // Soft delete

            return response()->json([
                'message' => 'Assignment deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Assignment not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function indexdelete()
    {
        $assignments = Assignment::onlyTrashed()->get();
        return response()->json($assignments);
    }
    public function restore($id)
    {
        try {
            $assignment = Assignment::onlyTrashed()->findOrFail($id);
            $assignment->restore(); // Mengembalikan data yang terhapus

            return response()->json([
                'message' => 'Assignment restored successfully',
                'data' => $assignment
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Assignment not found or not deleted'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function forceDelete($id)
    {
        try {
            // Cari data berdasarkan ID
            $assignment = Assignment::find($id);

            // Jika tidak ditemukan, kembalikan response error
            if (!$assignment) {
                return response()->json(['message' => 'Assignment not found'], 404);
            }

            // Hapus gambar dari storage jika ada
            if ($assignment->image) {
                Storage::disk('public')->delete($assignment->image);
            }

            // Hapus assignment dari database
            $assignment->delete();

            return response()->json(['message' => 'Assignment deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
    public function countAssignments(Request $request)
    {
        // Validasi input untuk memastikan role_id diisi dan ada di database
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id'
        ]);

        $role_id = $request->query('role_id');

        // Hitung jumlah assignment berdasarkan role_id
        $count = Assignment::where(function ($query) use ($role_id) {
            $query->where('role_by', $role_id)
                ->orWhere('role_to', $role_id);
        })->count();

        return response()->json([
            'message' => $count > 0 ? 'Total assignments counted successfully' : 'No assignments found for this role',
            'total_assignments' => $count
        ], 200);
    }

    public function show_all()
    {
        $assignment = Assignment::with('user', 'role')->get();

        if ($assignment->isEmpty()) {
            return response()->json(['message' => 'No assignments found'], 404);
        }

        return AssignmentResource::collection($assignment);
    }

    public function show_summary(Request $request): JsonResponse
    {
        $roleIdSelected = $request->input('role_id_selected', 0); // Default 0 jika tidak dikirim

        $assignments = DB::table('assignments as a')
            ->leftJoin('users as b', 'a.user_id_to', '=', 'b.id')
            ->leftJoin('roles as c', 'a.role_to', '=', 'c.id')
            ->selectRaw('
            b.id AS userToId,
            b.name AS userToName,
            c.id AS roleToId,
            c.name AS roleToName,
            COUNT(*) AS jumlah_tugas,
            SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) AS jumlah_Unfinish,
            SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) AS jumlah_On_Progress,
            SUM(CASE WHEN a.status = 2 THEN 1 ELSE 0 END) AS jumlah_selesai
        ')
            ->where('a.role_to', '>=', $roleIdSelected)
            ->groupBy('b.id', 'b.name', 'c.id', 'c.name')
            ->havingRaw('jumlah_selesai != jumlah_tugas')
            ->orderBy('c.name')
            ->orderBy('b.name')
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json([
                'message' => 'No assignments found',
                'roleIdSelected' => $roleIdSelected,
                'data' => $assignments
            ]);
        }

        // Casting nilai agar integer (agar tidak jadi string di hosting)
        $assignments = $assignments->map(function ($item) {
            return [
                'userToId' => (int) $item->userToId,
                'userToName' => $item->userToName,
                'roleToId' => (int) $item->roleToId,
                'roleToName' => $item->roleToName,
                'jumlah_tugas' => (int) $item->jumlah_tugas,
                'jumlah_Unfinish' => (int) $item->jumlah_Unfinish,
                'jumlah_On_Progress' => (int) $item->jumlah_On_Progress,
                'jumlah_selesai' => (int) $item->jumlah_selesai,
            ];
        });

        return response()->json($assignments);
    }

    public function exportSummary(Request $request)
    {
        $roleIdSelected = $request->input('role_id_selected', 0);

        return Excel::download(new AssignmentSummaryExport($roleIdSelected), 'assignment_summary.xlsx');
    }
}
