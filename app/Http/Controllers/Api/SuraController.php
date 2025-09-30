<?php

namespace App\Http\Controllers\Api;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuranText;
use App\Models\Sura;

class SuraController extends Controller
{
    // Get all suras with meta only
    public function suraList(Request $request)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
            $perPage = $request->limit ?? 10;
            $page = $request->page ?? 1;

            // $suras = Sura::select('id','sura_number','arabic_name','german_name','total_ayas','revelation_type')
            // ->orderBy('sura_number')
            // ->get();
            $query = Sura::select('id','sura_number','arabic_name','german_name','total_ayas','revelation_type')->orderBy('sura_number');

            $suras = $query->paginate($request->limit ?? 10);

            return response()->json([
                'success' => true,
                'message' => "Sura retrive successfully.",
                'data' => [
                    'suras'         => $suras->items(),
                    'current_page' => $suras->currentPage(),
                    'last_page'    => $suras->lastPage(),
                    'per_page'     => $suras->perPage(),
                    'total'        => $suras->total()
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $th->getMessage(),
            ], 500);
        }   

    }

    public function getSuraById1(Request $id)
    {
        $sura = Sura::with(['ayas' => function($q) {
            $q->orderBy('aya');
        }])->findOrFail($id);

        // Combine all ayas into a single string with Arabic numbers
        $combinedText = $sura->ayas->map(function($aya) {
            // Convert aya number to Arabic-Indic numerals
            $arabicNumber = $this->convertToArabicNumber($aya->aya);
            return trim($aya->text) . ' ' . $arabicNumber;
        })->implode(' ');

        // Prepare response
        $response = [
            'id' => $sura->id,
            'sura_number' => $sura->sura_number,
            'arabic_name' => $sura->name_ar,
            'german_name' => $sura->name_en,
            'total_ayas' => $sura->total_ayas,
            'revelation_type' => $sura->revelation_type,
            'text' => $combinedText
        ];

        return response()->json($response);
    }

    public function getSuraById2($id)
    {
        try {
            $sura = Sura::with(['ayas' => function($q) {
                $q->orderBy('aya');
            }])->findOrFail($id);

            // Combine all ayas into a single string with Arabic numbers
            $combinedText = $sura->ayas->map(function($aya) {
                $arabicNumber = $this->convertToArabicNumber($aya->aya);
                return trim($aya->text) . ' ' . $arabicNumber;
            })->implode(' ');

            // Prepare response
            $data = [
                'id' => $sura->id,
                'sura_number' => $sura->sura_number,
                'arabic_name' => $sura->arabic_name,
                'german_name' => $sura->german_name,
                'total_ayas' => $sura->total_ayas,
                'revelation_type' => $sura->revelation_type,
                'text' => $combinedText
            ];

            return response()->json([
                'success' => true,
                'message' => "Sura get successfully.",
                'data'    => $data
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sura not found',
                'error' => $e->getMessage(),
            ], 404);
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

    /**
     * Convert standard number to Arabic-Indic numerals
     */
    // private function convertToArabicNumber($number)
    // {
    //     $western = ['0','1','2','3','4','5','6','7','8','9'];
    //     $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];

    //     return str_replace($western, $arabic, $number);
    // }
    // ----------------------------------------------------------------------------
    // Get all suras with meta only
    // public function getAllSura()
    // {
    //     $suras = Sura::select('id','sura_number','arabic_name','german_name','total_ayas','revelation_type')
    //         ->orderBy('sura_number')
    //         ->get();

    //     return response()->json($suras);
    // }

    // Get a single sura with all ayas
    // public function getSuraById($id)
    // {
    //     $sura = Sura::with(['ayas' => function($q) {
    //         $q->orderBy('aya');
    //     }])->findOrFail($id);

    //     return response()->json($sura);
    // }

    public function getSuraById(Request $request, $id)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
            $perPage = $request->limit ?? 10;
            $page = $request->page ?? 1;

            $sura = Sura::with(['ayas'])->findOrFail($id);
            $query = QuranText::where('sura', $sura->sura_number)->orderBy('aya');

            $ayas = $query->paginate($request->limit ?? 10);

            // Optionally convert aya numbers to Arabic-Indic and combine text
            $combinedText = $ayas->getCollection()->map(function($aya) {
                $arabicNumber = $this->convertToArabicNumber($aya->aya);
                return trim($aya->text) . ' ' . $arabicNumber;
            })->implode(' ');

            // Response
            return response()->json([
                'success' => true,
                'suraDetail' => [
                    'id' => $sura->id,
                    'sura_number' => $sura->sura_number,
                    'arabic_name' => $sura->arabic_name,
                    'german_name' => $sura->german_name,
                    'total_ayas' => $sura->total_ayas,
                    'revelation_type' => $sura->revelation_type,
                ],
                'data' => [
                    'ayas' => $ayas->items(),
                    'ayasInSuraFormate' => $combinedText,
                    'current_page' => $ayas->currentPage(),
                    'last_page' => $ayas->lastPage(),
                    'per_page' => $ayas->perPage(),
                    'total' => $ayas->total()
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sura not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert standard number to Arabic-Indic numerals
     */
    private function convertToArabicNumber($number)
    {
        $western = ['0','1','2','3','4','5','6','7','8','9'];
        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];

        return str_replace($western, $arabic, $number);
    }

}
