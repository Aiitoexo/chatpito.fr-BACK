<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .container { padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: bold; color: #FF6B9D; }
        .invoice-title { font-size: 20px; font-weight: bold; color: #333; text-align: right; }
        .invoice-meta { text-align: right; margin-top: 5px; color: #666; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #555; border-bottom: 2px solid #FF6B9D; padding-bottom: 5px; }
        .info-grid { width: 100%; }
        .info-grid td { vertical-align: top; width: 50%; padding: 5px 0; }
        .info-block { line-height: 1.6; }
        .info-block strong { display: block; margin-bottom: 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th { background-color: #FF6B9D; color: white; padding: 10px 8px; text-align: left; font-size: 11px; }
        table.items td { padding: 8px; border-bottom: 1px solid #eee; }
        table.items tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .totals { width: 300px; margin-left: auto; margin-top: 20px; }
        .totals td { padding: 6px 8px; }
        .totals .total-row { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 10px; color: #999; text-align: center; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table style="width: 100%; margin-bottom: 40px;">
            <tr>
                <td style="vertical-align: top;">
                    <div class="logo">Chatpito.fr</div>
                    <div style="color: #666; margin-top: 5px;">Confiseries & Gourmandises</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="invoice-title">FACTURE</div>
                    <div class="invoice-meta">
                        N° {{ $invoice->invoice_number }}<br>
                        Date : {{ $invoice->issued_at->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Vendeur / Client -->
        <table class="info-grid" style="margin-bottom: 30px;">
            <tr>
                <td>
                    <div class="info-block">
                        <strong>Vendeur</strong>
                        Chatpito.fr<br>
                        [Adresse de la société]<br>
                        [Code postal, Ville]<br>
                        SIRET : [Numéro SIRET]<br>
                        TVA Intracom. : [Numéro TVA]
                    </div>
                </td>
                <td>
                    <div class="info-block">
                        <strong>Client</strong>
                        {{ $order->shipping_name }}<br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_zip }} {{ $order->shipping_city }}<br>
                        {{ $order->shipping_email }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Articles -->
        <div class="section">
            <div class="section-title">Articles commandés</div>
            <table class="items">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Variante</th>
                        <th class="text-right">Qté</th>
                        <th class="text-right">Prix unit. TTC</th>
                        <th class="text-right">Total TTC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->variant_name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 2, ',', ' ') }} €</td>
                        <td class="text-right">{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totaux -->
        <table class="totals">
            <tr>
                <td>Total HT</td>
                <td class="text-right">{{ number_format($order->subtotal_ht, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td>TVA</td>
                <td class="text-right">{{ number_format($order->tax_amount, 2, ',', ' ') }} €</td>
            </tr>
            <tr class="total-row">
                <td>Total TTC</td>
                <td class="text-right">{{ number_format($order->total, 2, ',', ' ') }} €</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            Chatpito.fr — [Raison sociale] — SIRET : [Numéro] — TVA Intracom. : [Numéro]<br>
            Conditions de paiement : paiement comptant à la commande.<br>
            En cas de retard de paiement, une pénalité de 3 fois le taux d'intérêt légal sera appliquée,
            ainsi qu'une indemnité forfaitaire de 40€ pour frais de recouvrement.
        </div>
    </div>
</body>
</html>
