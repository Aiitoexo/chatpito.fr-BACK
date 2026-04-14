<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductImportController extends Controller
{
    public function import(Request $request): JsonResponse
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        $headers = fgetcsv($handle, 0, ';');
        $headers = array_map(fn ($h) => trim(strtolower($h)), $headers);

        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = array_combine($headers, $row);

            try {
                $category = Category::where('name', $data['categorie'] ?? '')->first();

                Product::updateOrCreate(
                    ['slug' => Str::slug($data['nom'] ?? '')],
                    [
                        'name' => $data['nom'] ?? '',
                        'description' => $data['description'] ?? '',
                        'description_courte' => $data['description_courte'] ?? null,
                        'marque' => $data['marque'] ?? null,
                        'price' => floatval($data['prix'] ?? 0),
                        'category_id' => $category?->id ?? 1,
                        'actif' => true,
                        'featured' => false,
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Ligne " . ($imported + count($errors) + 2) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'imported' => $imported,
            'errors' => $errors,
        ]);
    }
}
