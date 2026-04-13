<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.variant.product', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    public function show(string $id)
    {
        return response()->json(
            Order::with(['items.variant.product', 'user'])->findOrFail($id)
        );
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,preparing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:50',
        ]);

        $order = Order::findOrFail($id);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'shipped') {
            $updateData['tracking_number'] = $validated['tracking_number'] ?? null;
            $updateData['carrier'] = $validated['carrier'] ?? null;
            $updateData['shipped_at'] = now();
        }

        $order->update($updateData);

        if ($validated['status'] === 'shipped') {
            Mail::to($order->shipping_email)->queue(new OrderShippedMail($order));
        }

        return response()->json($order->load(['items.variant.product', 'user']));
    }

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'status' => 'nullable|string',
        ]);

        $orders = Order::with(['items.variant.product', 'user'])
            ->whereBetween('created_at', [$validated['from'], $validated['to'] . ' 23:59:59'])
            ->when($validated['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = ['N° commande', 'Date', 'Client', 'Email', 'Statut', 'Montant HT', 'TVA', 'Total TTC', 'Livraison', 'N° suivi'];

        return response()->streamDownload(function () use ($orders, $headers) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, $headers, ';');

            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->id,
                    $order->created_at->format('d/m/Y'),
                    $order->user?->name ?? $order->shipping_name,
                    $order->user?->email ?? $order->shipping_email,
                    $order->status,
                    number_format($order->subtotal_ht, 2, ',', ''),
                    number_format($order->tax_amount, 2, ',', ''),
                    number_format($order->total, 2, ',', ''),
                    $order->carrier ?? '',
                    $order->tracking_number ?? '',
                ], ';');
            }

            fclose($out);
        }, 'commandes.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
