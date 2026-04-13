<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #FF6B9D, #9B5DE5, #4ECDC4); padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 28px; }
        .header p { color: rgba(255,255,255,0.9); margin: 5px 0 0; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; color: #333; margin-bottom: 20px; }
        .order-info { background-color: #f9f9f9; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .order-info h3 { margin: 0 0 10px; color: #333; }
        .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .item:last-child { border-bottom: none; }
        .total { font-size: 20px; font-weight: bold; color: #FF6B9D; text-align: right; margin-top: 15px; }
        .address { background-color: #f0f9f8; border-radius: 12px; padding: 15px; margin-bottom: 20px; }
        .cta { display: inline-block; background-color: #4ECDC4; color: white; padding: 14px 30px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-top: 10px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chatpito.fr</h1>
            <p>Votre commande est confirmée !</p>
        </div>

        <div class="content">
            <p class="greeting">
                Bonjour {{ $order->shipping_name }},
            </p>
            <p>Merci pour votre commande ! Voici le récapitulatif :</p>

            <div class="order-info">
                <h3>Commande #{{ $order->id }}</h3>
                @foreach($order->items as $item)
                <div class="item">
                    <span>{{ $item->product_name }} ({{ $item->variant_name }}) x{{ $item->quantity }}</span>
                    <span>{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</span>
                </div>
                @endforeach
                <div class="total">
                    Total : {{ number_format($order->total, 2, ',', ' ') }} €
                </div>
            </div>

            <div class="address">
                <strong>Adresse de livraison</strong><br>
                {{ $order->shipping_name }}<br>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_zip }} {{ $order->shipping_city }}
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/compte/commandes/{{ $order->id }}" class="cta">
                    Suivre ma commande
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Chatpito.fr — Confiseries & Gourmandises</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
