<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{

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


    public function index()
    {
        $currentPage = ($request->page == 0) ? 1 : $request->page;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        return response()->json(Contact::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $contact = Contact::create($validated);
        return response()->json([
            'success' => true,
            'data' => $contact,
        ], 201);

        return response()->json($contact, 201);
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact, 200);
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:contacts,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
        ]);

        $contact->update($validated);

        return response()->json($contact, 200);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contact deleted'], 200);
    }
}
