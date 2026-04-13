<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();

        return response()->json(['data' => $addresses]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'sometimes|string|max:50',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'country' => 'sometimes|string|max:2',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'sometimes|boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        if (!empty($validated['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $isFirst = $request->user()->addresses()->count() === 0;
        if ($isFirst) {
            $validated['is_default'] = true;
        }

        $address = Address::create($validated);

        return response()->json(['data' => $address], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label' => 'sometimes|string|max:50',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'address_line1' => 'sometimes|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'sometimes|string|max:10',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:2',
            'phone' => 'nullable|string|max:20',
        ]);

        $address->update($validated);

        return response()->json(['data' => $address]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json(null, 204);
    }

    public function setDefault(Request $request, string $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json(['data' => $address]);
    }
}
