<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #26DE81, #4ECDC4); padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { padding: 30px; text-align: center; }
        .product-name { font-size: 22px; font-weight: bold; color: #333; margin: 20px 0 10px; }
        .cta { display: inline-block; background-color: #FF6B9D; color: white; padding: 14px 30px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bonne nouvelle !</h1>
        </div>
        <div class="content">
            <p class="product-name">{{ $variant->product->name }}</p>
            <p style="color: #666;">Le format <strong>{{ $variant->nom }}</strong> est de retour en stock.</p>
            <p style="color: #26DE81; font-weight: bold;">{{ number_format($variant->prix_vente_ttc, 2, ',', ' ') }} €</p>
            <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/produit/{{ $variant->product->slug }}" class="cta">
                Voir le produit
            </a>
        </div>
        <div class="footer">
            <p>Chatpito.fr — Confiseries & Gourmandises</p>
        </div>
    </div>
</body>
</html>
