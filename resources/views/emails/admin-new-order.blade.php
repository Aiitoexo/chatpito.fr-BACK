<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background-color: #333; padding: 20px; text-align: center; }
        .header h1 { color: #FF6B9D; margin: 0; font-size: 22px; }
        .content { padding: 30px; }
        .info { margin-bottom: 15px; }
        .info strong { display: inline-block; width: 140px; color: #666; }
        .items { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items th { background-color: #f5f5f5; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        .items td { padding: 8px 10px; border-bottom: 1px solid #eee; }
        .total { font-size: 18px; font-weight: bold; text-align: right; color: #FF6B9D; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouvelle commande #{{ $order->id }}</h1>
        </div>

        <div class="content">
            <div class="info"><strong>Client :</strong> {{ $order->shipping_name }} ({{ $order->shipping_email }})</div>
            <div class="info"><strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
            <div class="info"><strong>Statut :</strong> {{ $order->status }}</div>

            <table class="items">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Variante</th>
                        <th>Qté</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->variant_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="info"><strong>HT :</strong> {{ number_format($order->subtotal_ht, 2, ',', ' ') }} €</div>
            <div class="info"><strong>TVA :</strong> {{ number_format($order->tax_amount, 2, ',', ' ') }} €</div>
            <div class="total">Total TTC : {{ number_format($order->total, 2, ',', ' ') }} €</div>

            <hr style="margin: 20px 0;">

            <div class="info"><strong>Livraison :</strong></div>
            <div>{{ $order->shipping_address }}, {{ $order->shipping_zip }} {{ $order->shipping_city }}</div>
        </div>
    </div>
</body>
</html>
