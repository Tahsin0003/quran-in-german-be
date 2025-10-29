<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuranText;


class VerseController extends Controller
{
    // Get all Verses with pagination, filtering, and sorting
    public function index(Request $request)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $query = QuranText::select('q.*')->from('quran_text as q');

            if (!empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('q.index', 'like', '%' . $request->search . '%')
                    ->orWhere('q.sura', 'like', '%' . $request->search . '%')
                    ->orWhere('q.aya', 'like', '%' . $request->search . '%')
                    ->orWhere('q.text', 'like', '%' . $request->search . '%');
                });
            }

            $orderDir = $request->order_dir ?? 'asc';
            $orderColumn = $request->order_by ?? 'q.index';
            $query->orderBy($orderColumn, $orderDir);

            $limit = $request->limit ?? 10;
            $suras = $query->paginate($limit);

            return response()->json([
                'success'      => true,
                'message'      => "Suras retrieved successfully.",
                'data'         => $suras->items(),
                'current_page' => $suras->currentPage(),
                'limit'        => $suras->perPage(),
                'last_page'    => $suras->lastPage(),
                'total'        => $suras->total()
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

    // Get a specific Verse by ID
    // Show single Verse
    // public function show($id)
    // {
    //     try {
    //         $sura = Sura::findOrFail($id);
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Sura found successfully.',
    //             'data'    => $sura
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Sura not found with ID ' . $id,
    //             'error'   => $e->getMessage(),
    //         ], 404);
    //     }
    // }
    public function show($id)
    {
        try {
            $verse = QuranText::find($id);
            if (!$verse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verse not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verse retrieved successfully',
                'data' => $verse
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Opps! Something went wrong',
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


    // Update user
    public function update(Request $request, $id)
    {
        try {
            $aayat = QuranText::findOrFail($id);
            $validated = $request->validate([
                'sura_no' => 'required|integer',
                'aya_no'  => 'required|integer',
                'text'    => 'required|string',
            ]);

            if (isset($validated['sura_no'])) {
                $aayat->sura = $validated['sura_no'];
            }

            if (isset($validated['aya'])) {
                $aayat->aya = $validated['aya'];
            }

            if (isset($validated['text'])) {
                $aayat->text = $validated['text'];
            }

            $aayat->save();

            return response()->json([
                'success' => true,
                'message' => 'Verse updated successfully.',
                'data'    => $aayat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    
    }
}
