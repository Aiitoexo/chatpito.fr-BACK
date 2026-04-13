<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function download(string $id): Response
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path)) {
            return response()->json(['message' => __('invoice.pdf_not_found')], 404);
        }

        return Storage::disk('local')->download(
            $invoice->pdf_path,
            "{$invoice->invoice_number}.pdf"
        );
    }
}
