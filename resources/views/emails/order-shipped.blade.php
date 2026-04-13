<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #4ECDC4, #9B5DE5); padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.9); margin: 5px 0 0; }
        .content { padding: 30px; }
        .tracking-box { background-color: #f0f9f8; border: 2px solid #4ECDC4; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
        .tracking-number { font-size: 22px; font-weight: bold; color: #333; letter-spacing: 2px; }
        .carrier { color: #666; margin-top: 5px; }
        .cta { display: inline-block; background-color: #4ECDC4; color: white; padding: 14px 30px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-top: 10px; }
        .items { margin: 20px 0; }
        .item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chatpito.fr</h1>
            <p>Votre commande est en route !</p>
        </div>

        <div class="content">
            <p>Bonjour {{ $order->shipping_name }},</p>
            <p>Bonne nouvelle ! Votre commande <strong>#{{ $order->id }}</strong> a été expédiée.</p>

            @if($order->tracking_number)
            <div class="tracking-box">
                <p style="color: #666; margin: 0 0 10px;">Numéro de suivi</p>
                <p class="tracking-number">{{ $order->tracking_number }}</p>
                @if($order->carrier)
                <p class="carrier">Transporteur : {{ ucfirst($order->carrier) }}</p>
                @endif
            </div>
            @endif

            <div class="items">
                <h3>Récapitulatif</h3>
                @foreach($order->items as $item)
                <div class="item">
                    <span>{{ $item->product_name }} ({{ $item->variant_name }}) x{{ $item->quantity }}</span>
                    <span>{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</span>
                </div>
                @endforeach
            </div>

            <p><strong>Livraison à :</strong><br>
            {{ $order->shipping_address }}, {{ $order->shipping_zip }} {{ $order->shipping_city }}</p>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/compte/commandes/{{ $order->id }}" class="cta">
                    Suivre ma commande
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Chatpito.fr — Confiseries & Gourmandises</p>
        </div>
    </div>
</body>
</html>
