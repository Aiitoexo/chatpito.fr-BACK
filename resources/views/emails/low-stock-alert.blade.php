<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background-color: #FF6B6B; padding: 20px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .content { padding: 30px; }
        .alert-box { background-color: #FFF3F3; border: 2px solid #FF6B6B; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
        .stock-number { font-size: 36px; font-weight: bold; color: #FF6B6B; }
        .info { margin: 10px 0; }
        .info strong { display: inline-block; width: 120px; color: #666; }
        .cta { display: inline-block; background-color: #FF6B9D; color: white; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-top: 15px; }
        .footer { background-color: #f5f5f5; padding: 15px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Alerte Stock Bas</h1>
        </div>
        <div class="content">
            <div class="alert-box">
                <p style="color: #666; margin: 0 0 10px;">Stock actuel</p>
                <p class="stock-number">{{ $stock->quantite_disponible }}</p>
                <p style="color: #999;">Seuil d'alerte : {{ $stock->seuil_alerte }}</p>
            </div>

            <div class="info"><strong>Produit :</strong> {{ $stock->variant->product->name ?? 'N/A' }}</div>
            <div class="info"><strong>Variante :</strong> {{ $stock->variant->nom ?? 'N/A' }}</div>
            <div class="info"><strong>SKU :</strong> {{ $stock->variant->sku ?? 'N/A' }}</div>

            @if($stock->variant->suppliers && $stock->variant->suppliers->count() > 0)
            <h3 style="margin-top: 20px; color: #333;">Fournisseurs</h3>
            @foreach($stock->variant->suppliers as $supplier)
            <div class="info"><strong>{{ $supplier->nom }}</strong> — {{ $supplier->email ?? 'Pas d\'email' }}</div>
            @endforeach
            @endif

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/admin/stocks" class="cta">
                    Gérer les stocks
                </a>
            </div>
        </div>
        <div class="footer">
            <p>Chatpito.fr — Alerte automatique</p>
        </div>
    </div>
</body>
</html>
