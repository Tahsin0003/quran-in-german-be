<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Models\VerseFootNote;
use Illuminate\Http\Request;


class VerseFootNoteController extends Controller
{
    // Get: show all Verse Foot Note
    public function index(Request $request, $id)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;

            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            // Base query
            $query = VerseFootNote::where('verse_id', $id);

            // Search filter
            if (!empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('id', 'like', '%' . $request->search . '%')
                        ->orWhere('title', 'like', '%' . $request->search . '%')
                        ->orWhere('content', 'like', '%' . $request->search . '%')
                        ->orWhere('order', 'like', '%' . $request->search . '%')
                        ->orWhere('status', 'like', '%' . $request->search . '%');
                });
            }

            $orderDir = $request->order_dir ?? 'asc';
            $orderColumn = $request->order_by ?? 'id';
            $query->orderBy($orderColumn, $orderDir);

            $limit = $request->limit ?? 10;
            $verseFootNotes = $query->paginate($limit);

            return response()->json([
                'success'      => true,
                'message'      => "Verse foot notes retrieved successfully.",
                'data'         => $verseFootNotes->items(),
                'current_page' => $verseFootNotes->currentPage(),
                'limit'        => $verseFootNotes->perPage(),
                'last_page'    => $verseFootNotes->lastPage(),
                'total'        => $verseFootNotes->total()
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database query error',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    // POST: Create a new Foot Note
    public function store(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'order'   => 'nullable|integer',
                'status'  => 'nullable',
            ]);

            $verseFootNote = new VerseFootNote();
            $verseFootNote->verse_id = $id;
            $verseFootNote->title    = $validated['title'] ?? null;
            $verseFootNote->content  = $validated['content'] ?? null;
            $verseFootNote->order    = $validated['order'] ?? null;
            $verseFootNote->status   = $validated['status'] ?? 0;
            $verseFootNote->save();

            return response()->json([
                'success' => true,
                'message' => 'Verse Foot Note Created Successfully.',
                'data'    => $verseFootNote
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $th->getMessage(),
            ], 500);

        }
    }


    // PUT: update a Foot Note
    public function update(Request $request, $id, $translateId)
    {
        try {
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'order'   => 'nullable|integer',
                'status'  => 'nullable',
            ]);

            $verseFootNote = VerseFootNote::where('verse_id', $id)->where('id', $translateId)->first();

            if (!$verseFootNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse Foot Note not found.'
                ], 404);
            }
            $verseFootNote->verse_id = $id;
            $verseFootNote->title = $validated['title'] ?? null;
            $verseFootNote->content = $validated['content'] ?? null;
            $verseFootNote->order = $validated['order'] ?? null;
            $verseFootNote->status = $validated['status'] ?? 0;
            $verseFootNote->save();

            return response()->json([
                'success' => true,
                'message' => 'Verse Foot Note updated successfully.',
                'data'    => $verseFootNote
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // GET: Show Verse Foot Note by Id
    public function show($id, $translateId)
    {
        try {
            $verseFootNote = VerseFootNote::where('verse_id', $id)->where('id', $translateId)->first();

            if (!$verseFootNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse Foot Note not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verse translation fetch successfully.',
                'data'    => $verseFootNote
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // DELETE: Remove Verse Translation
    public function destroy($id, $translateId)
    {
        try {
            $verseFootNote = VerseFootNote::where('verse_id', $id)->where('id', $translateId)->first();

            if (!$verseFootNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse Foot Note not found.'
                ], 404);
            }

            $verseFootNote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Verse Foot Noot deleted successfully.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
