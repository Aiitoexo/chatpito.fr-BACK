<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #FF6B9D, #9B5DE5); padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total { font-size: 20px; font-weight: bold; color: #FF6B9D; text-align: right; margin-top: 15px; }
        .cta { display: inline-block; background-color: #4ECDC4; color: white; padding: 14px 30px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vous avez oublié quelque chose...</h1>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Vos gourmandises vous attendent ! Voici ce que vous aviez dans votre panier :</p>

            @foreach($cart->items as $item)
            <div class="item">
                <span>{{ $item['productName'] ?? $item['product_name'] ?? 'Produit' }} x{{ $item['quantity'] }}</span>
                <span>{{ number_format(($item['prixTtc'] ?? $item['price'] ?? 0) * $item['quantity'], 2, ',', ' ') }} €</span>
            </div>
            @endforeach

            <div class="total">
                Total : {{ number_format($cart->total, 2, ',', ' ') }} €
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/panier" class="cta">
                    Reprendre ma commande
                </a>
            </div>
        </div>
        <div class="footer">
            <p>Chatpito.fr — Confiseries & Gourmandises</p>
            <p>Si vous ne souhaitez plus recevoir ces emails, ignorez simplement ce message.</p>
        </div>
    </div>
</body>
</html>
