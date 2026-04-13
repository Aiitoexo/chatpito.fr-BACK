<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTagController extends Controller
{
    public function index()
    {
        return response()->json(
            Tag::withCount('products')->orderBy('nom')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);

        if (Tag::where('slug', $validated['slug'])->exists()) {
            return response()->json(['error' => 'Ce tag existe déjà'], 422);
        }

        $tag = Tag::create($validated);
        return response()->json($tag, 201);
    }

    public function show(string $id)
    {
        return response()->json(Tag::withCount('products')->findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
        ]);

        if (isset($validated['nom'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $tag->update($validated);
        return response()->json($tag);
    }

    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->products()->detach();
        $tag->delete();
        return response()->json(['message' => 'Tag supprimé']);
    }
}
