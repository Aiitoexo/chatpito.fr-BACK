<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AdminSupplierController extends Controller
{
    public function index()
    {
        return response()->json(
            Supplier::withCount('variants')->orderBy('nom')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'raison_sociale' => 'nullable|string|max:255',
            'siret' => 'nullable|string|max:14',
            'tva_intracommunautaire' => 'nullable|string|max:20',
            'contact_nom' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);
        return response()->json($supplier, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Supplier::with(['variants.product'])->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'raison_sociale' => 'nullable|string|max:255',
            'siret' => 'nullable|string|max:14',
            'tva_intracommunautaire' => 'nullable|string|max:20',
            'contact_nom' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);
        return response()->json($supplier);
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response()->json(['message' => 'Fournisseur supprimé']);
    }
}
