<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\Models\SuraSummary;
use App\Models\Sura;

class SuraSummaryController extends Controller
{
    // Get show all Sura Summary
    public function index(Request $request, $id)
    {
        // return $id;
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;

            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            // Base query
            $query = SuraSummary::where('sura_id', $id);

            // Search filter
            if (!empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('id', 'like', '%' . $request->search . '%')
                        ->orWhere('title', 'like', '%' . $request->search . '%')
                        ->orWhere('summary', 'like', '%' . $request->search . '%')
                        ->orWhere('order', 'like', '%' . $request->search . '%')
                        ->orWhere('status', 'like', '%' . $request->search . '%');
                });
            }

            // $query = SuraSummary::select('s.*')->from('sura_summaries as s')->where('sura_id', $id);

            // if (!empty($request->search)) {
            //     $query->where(function ($q) use ($request) {
            //         $q->where('s.id', 'like', '%' . $request->search . '%')
            //         ->orWhere('s.title', 'like', '%' . $request->search . '%')
            //         ->orWhere('s.summary', 'like', '%' . $request->search . '%')
            //         ->orWhere('s.order', 'like', '%' . $request->search . '%')
            //         ->orWhere('s.status', 'like', '%' . $request->search . '%');
            //     });
            // }

            $orderDir = $request->order_dir ?? 'asc';
            $orderColumn = $request->order_by ?? 'id';
            $query->orderBy($orderColumn, $orderDir);

            $limit = $request->limit ?? 10;
            $sura_summary = $query->paginate($limit);

            return response()->json([
                'success'      => true,
                'message'      => "Suras summary retrieved successfully.",
                'data'         => $sura_summary->items(),
                'current_page' => $sura_summary->currentPage(),
                'limit'        => $sura_summary->perPage(),
                'last_page'    => $sura_summary->lastPage(),
                'total'        => $sura_summary->total()
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

    // POST create a new sura summary
    public function store(Request $request, $id)
    {
        // return $request;
        try {
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'summary' => 'required|string',
                'order'   => 'nullable|integer',
                'status'  => 'nullable',
            ]);

            $summary = new SuraSummary();
            $summary->sura_id = $id;
            $summary->title = $validated['title'] ?? null;
            $summary->summary = $validated['summary'] ?? null;
            $summary->order = $validated['order'] ?? null;
            $summary->status = $validated['status'] ?? 0;
            $summary->save();

            return response()->json([
                'success' => true,
                'message' => 'Sura summary created successfully.',
                'data'    => $summary
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

    // PUT update a new sura summary
    public function update(Request $request, $id, $summaryId)
    {
        // return $summaryId;
        try {
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'summary' => 'required|string',
                'order'   => 'nullable|integer',
                'status'  => 'nullable',
            ]);

            // $summary = new SuraSummary();
            $summary = SuraSummary::where('sura_id', $id)->where('id', $summaryId)->first();

            if (!$summary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sura summary not found.'
                ], 404);
            }
            $summary->sura_id = $id;
            $summary->title = $validated['title'] ?? null;
            $summary->summary = $validated['summary'] ?? null;
            $summary->order = $validated['order'] ?? null;
            $summary->status = $validated['status'] ?? 0;
            $summary->save();

            return response()->json([
                'success' => true,
                'message' => 'Sura summary updated successfully.',
                'data'    => $summary
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

     // GET: Show Sura Summary
    public function show($id, $summaryId)
    {
        try {
            $summary = SuraSummary::where('sura_id', $id)->where('id', $summaryId)->first();

            if (!$summary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sura summary not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sura summary fetch successfully.',
                'data'    => $summary
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }


    // DELETE: Remove Sura Summary
    public function destroy($id, $summaryId)
    {
        try {
            $summary = SuraSummary::where('sura_id', $id)->where('id', $summaryId)->first();

            if (!$summary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sura summary not found.'
                ], 404);
            }

            $summary->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sura summary deleted successfully.'
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
