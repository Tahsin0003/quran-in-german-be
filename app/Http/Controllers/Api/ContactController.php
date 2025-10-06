<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{

    // Get all Contacts
    public function index(Request $request)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
            
            $query = Contact::select("c.*")->from("contacts as c")->orderBy("c.created_at", "DESC");
            $contacts = $query->paginate($request->limit ?? 10);

            return response()->json([
                'success'      => true,
                'message'      => "Contact us list retrive successfully.",
                'data'         => $contacts->items(),
                'current_page' => $contacts->currentPage(),
                'limit'        => $contacts->perPage(),
                'last_page'    => $contacts->lastPage(),
                'total'        => $contacts->total()
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

    // Show single contact
    public function show($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Contact found successfully.',
                'data'    => $contact
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found with ID'
                // 'error'   => $e->getMessage(),
            ], 404);
        }
    }

    public function contactUs(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email',
                'phone'   => 'required|string|max:20',
                'message' => 'required|string',
            ]);

            // Save contact
            $contact = new Contact();
            $contact->name    = $validated['name'];
            $contact->email   = $validated['email'];
            $contact->phone   = $validated['phone'];
            $contact->message = $validated['message'];
            $contact->save();

            // Success response
            return response()->json([
                'success' => true,
                'message' => 'Your request has been submitted successfully. We will get back to you shortly.',
            ], 201);

        } catch (\Exception $e) {
            // Error response
            return response()->json([
                'success' => false,
                'message' => 'Oops! Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return response()->json(['message' => 'Contact deleted'], 200);
    }

    public function getContacts(Request $request)
    {
        $currentPage = ($request->page == 0) ? 1 : $request->page;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = Contact::select("c.*")->from("contacts as c")->orderBy("c.created_at", "DESC");
        $limit = $request->limit ?? 10;
        $contacts = $query->paginate($limit);
        
        return response()->json([
            'success'      => true,
            'data'         => $contacts->items(),
            'current_page' => $contacts->currentPage(),
            'limit'        => $contacts->perPage(),
            'last_page'    => $contacts->lastPage(),
            'total'        => $contacts->total(),
        ]);
    }

    // public function index1()
    // {
    //     $currentPage = ($request->page == 0) ? 1 : $request->page;
    //     Paginator::currentPageResolver(function () use ($currentPage) {
    //         return $currentPage;
    //     });
    //     return response()->json(Contact::all(), 200);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'  => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'phone' => 'nullable|string|max:20',
    //         'address' => 'nullable|string',
    //     ]);

    //     $contact = Contact::create($validated);
    //     return response()->json([
    //         'success' => true,
    //         'data' => $contact,
    //     ], 201);

    //     return response()->json($contact, 201);
    // }

    // public function show1($id)
    // {
    //     $contact = Contact::findOrFail($id);
    //     return response()->json($contact, 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $contact = Contact::findOrFail($id);
    //     $validated = $request->validate([
    //         'name'  => 'sometimes|string|max:255',
    //         'email' => 'sometimes|email|unique:contacts,email,'.$id,
    //         'phone' => 'nullable|string|max:20',
    //         'message' => 'nullable|string',
    //     ]);
    //     $contact->update($validated);
    //     return response()->json($contact, 200);
    // }
}
