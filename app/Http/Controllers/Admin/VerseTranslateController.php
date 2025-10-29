<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Models\VerseTranslate;
use Illuminate\Http\Request;

class VerseTranslateController extends Controller
{
    // Get: show all Verse in German Translate
    public function index(Request $request, $id)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;

            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            // Base query
            $query = VerseTranslate::where('verse_id', $id);

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
            $verseTranslates = $query->paginate($limit);

            return response()->json([
                'success'      => true,
                'message'      => "Verse Translation retrieved successfully.",
                'data'         => $verseTranslates->items(),
                'current_page' => $verseTranslates->currentPage(),
                'limit'        => $verseTranslates->perPage(),
                'last_page'    => $verseTranslates->lastPage(),
                'total'        => $verseTranslates->total()
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


    // POST: Create a new Verse in German Translate
    public function store(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'order'   => 'nullable|integer',
                'status'  => 'nullable',
            ]);

            $verseTranslate = new VerseTranslate();
            $verseTranslate->verse_id = $id;
            $verseTranslate->title    = $validated['title'] ?? null;
            $verseTranslate->content  = $validated['content'] ?? null;
            $verseTranslate->order    = $validated['order'] ?? null;
            $verseTranslate->status   = $validated['status'] ?? 0;
            $verseTranslate->save();

            return response()->json([
                'success' => true,
                'message' => 'Verse in German Translate created successfully.',
                'data'    => $verseTranslate
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


    // PUT: update a Verse in German Translate
    public function update(Request $request, $id, $translateId)
    {
        try {
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'order'   => 'nullable|integer',
                'status'  => 'nullable',
            ]);

            $verseTranslate = VerseTranslate::where('verse_id', $id)->where('id', $translateId)->first();

            if (!$verseTranslate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse translate not found.'
                ], 404);
            }
            $verseTranslate->verse_id = $id;
            $verseTranslate->title = $validated['title'] ?? null;
            $verseTranslate->content = $validated['content'] ?? null;
            $verseTranslate->order = $validated['order'] ?? null;
            $verseTranslate->status = $validated['status'] ?? 0;
            $verseTranslate->save();

            return response()->json([
                'success' => true,
                'message' => 'Verse translate updated successfully.',
                'data'    => $verseTranslate
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

    // GET: Show Verse Translated by Id
    public function show($id, $translateId)
    {
        try {
            $verseTranslate = VerseTranslate::where('verse_id', $id)->where('id', $translateId)->first();

            if (!$verseTranslate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse translation not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verse translation fetch successfully.',
                'data'    => $verseTranslate
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
            $verseTranslate = VerseTranslate::where('verse_id', $id)->where('id', $translateId)->first();

            if (!$verseTranslate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse translation not found.'
                ], 404);
            }

            $verseTranslate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Verse translation deleted successfully.'
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
