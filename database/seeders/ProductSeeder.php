<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\NutritionalInfo;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tag;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Catégories hiérarchiques ───────────────────────
        $bonbons = Category::updateOrCreate(
            ['slug' => 'bonbons'],
            ['name' => 'Bonbons', 'image' => null, 'parent_id' => null, 'ordre' => 0, 'actif' => true]
        );

        $subCategories = [
            ['name' => 'Bonbons Classiques', 'slug' => 'bonbons-classiques', 'ordre' => 1],
            ['name' => 'Bonbons Acides', 'slug' => 'bonbons-acides', 'ordre' => 2],
            ['name' => 'Guimauves', 'slug' => 'guimauves', 'ordre' => 3],
        ];

        $cats = [];
        foreach ($subCategories as $sub) {
            $cats[$sub['slug']] = Category::updateOrCreate(
                ['slug' => $sub['slug']],
                ['name' => $sub['name'], 'parent_id' => $bonbons->id, 'ordre' => $sub['ordre'], 'actif' => true]
            );
        }

        $chocolats = Category::updateOrCreate(
            ['slug' => 'chocolats'],
            ['name' => 'Chocolats', 'parent_id' => null, 'ordre' => 1, 'actif' => true]
        );

        $sucettes = Category::updateOrCreate(
            ['slug' => 'sucettes'],
            ['name' => 'Sucettes', 'parent_id' => null, 'ordre' => 2, 'actif' => true]
        );

        $reglisse = Category::updateOrCreate(
            ['slug' => 'reglisse'],
            ['name' => 'Réglisse', 'parent_id' => null, 'ordre' => 3, 'actif' => true]
        );

        // ─── Tags ───────────────────────────────────────────
        $tagNames = ['bio', 'sans-gluten', 'vegan', 'nouveauté', 'promo', 'halal'];
        $tags = [];
        foreach ($tagNames as $name) {
            $tags[$name] = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['nom' => ucfirst($name)]
            );
        }

        // ─── Fournisseurs ───────────────────────────────────
        $haribo = Supplier::updateOrCreate(
            ['email' => 'contact@haribo.fr'],
            [
                'nom' => 'Haribo France',
                'raison_sociale' => 'HARIBO SAS',
                'siret' => '31558021300045',
                'contact_nom' => 'Jean Dupont',
                'telephone' => '01 23 45 67 89',
                'adresse' => '67 rue de la Confiserie',
                'code_postal' => '13015',
                'ville' => 'Marseille',
                'pays' => 'France',
            ]
        );

        $lutti = Supplier::updateOrCreate(
            ['email' => 'pro@lutti.fr'],
            [
                'nom' => 'Lutti Distribution',
                'raison_sociale' => 'LUTTI SAS',
                'siret' => '44498723100012',
                'contact_nom' => 'Marie Martin',
                'telephone' => '03 45 67 89 01',
                'adresse' => '12 avenue du Bonbon',
                'code_postal' => '59000',
                'ville' => 'Lille',
                'pays' => 'France',
            ]
        );

        // ─── Produits avec variantes ────────────────────────
        $productsData = [
            [
                'name' => 'Fraises Tagada',
                'description_courte' => 'Les célèbres fraises Tagada Haribo, un classique intemporel.',
                'description_longue' => 'Les Fraises Tagada sont des bonbons iconiques à la fraise, enrobés de sucre. Leur goût fruité et leur texture moelleuse en font un favori depuis des générations.',
                'marque' => 'Haribo',
                'category' => 'bonbons-classiques',
                'featured' => true,
                'tags' => ['halal'],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.90, 'ttc' => 2.00, 'poids' => 100, 'stock' => 150],
                    ['nom' => '250g', 'ht' => 3.32, 'ttc' => 3.50, 'poids' => 250, 'stock' => 80],
                    ['nom' => '500g', 'ht' => 6.16, 'ttc' => 6.50, 'poids' => 500, 'stock' => 40],
                ],
                'nutrition' => ['energie_kcal' => 337, 'energie_kj' => 1430, 'matieres_grasses' => 0.5, 'dont_acides_gras_satures' => 0.1, 'glucides' => 82, 'dont_sucres' => 58, 'proteines' => 3.6, 'ingredients' => 'Sucre, sirop de glucose, gélatine, amidon, acidifiant (acide citrique), arôme, colorant (rouge de cochenille)', 'allergenes' => 'Peut contenir des traces de lait'],
            ],
            [
                'name' => 'Crocodiles Haribo',
                'description_courte' => 'Des crocodiles gélifiés bicolores au goût fruité intense.',
                'description_longue' => 'Les Crocodiles Haribo combinent deux saveurs fruitées dans un seul bonbon gélifié. Leur forme amusante et leur texture rebondissante plaisent à tous les âges.',
                'marque' => 'Haribo',
                'category' => 'bonbons-classiques',
                'featured' => true,
                'tags' => [],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.52, 'ttc' => 1.60, 'poids' => 100, 'stock' => 120],
                    ['nom' => '250g', 'ht' => 2.75, 'ttc' => 2.90, 'poids' => 250, 'stock' => 60],
                ],
                'nutrition' => ['energie_kcal' => 343, 'energie_kj' => 1456, 'matieres_grasses' => 0.4, 'dont_acides_gras_satures' => 0.1, 'glucides' => 77, 'dont_sucres' => 55, 'proteines' => 6.2, 'ingredients' => 'Sirop de glucose, sucre, gélatine, amidon, acidifiants, arômes, colorants'],
            ],
            [
                'name' => 'Nounours à la Guimauve',
                'description_courte' => 'De tendres oursons en guimauve au goût vanillé.',
                'description_longue' => 'Les Nounours à la Guimauve sont des bonbons moelleux en forme d\'ourson. Leur texture fondante et leur saveur vanillée en font un incontournable des confiseries.',
                'marque' => 'Lutti',
                'category' => 'guimauves',
                'featured' => true,
                'tags' => ['sans-gluten'],
                'supplier' => $lutti,
                'variants' => [
                    ['nom' => '100g', 'ht' => 2.18, 'ttc' => 2.30, 'poids' => 100, 'stock' => 90],
                    ['nom' => '250g', 'ht' => 3.98, 'ttc' => 4.20, 'poids' => 250, 'stock' => 45],
                ],
                'nutrition' => ['energie_kcal' => 330, 'energie_kj' => 1400, 'matieres_grasses' => 0.1, 'dont_acides_gras_satures' => 0.0, 'glucides' => 80, 'dont_sucres' => 60, 'proteines' => 3.0, 'ingredients' => 'Sucre, sirop de glucose, gélatine, amidon, arôme vanille, colorants'],
            ],
            [
                'name' => 'Têtes Brûlées',
                'description_courte' => 'Bonbons ultra-acides qui piquent fort puis deviennent doux.',
                'description_longue' => 'Les Têtes Brûlées sont célèbres pour leur acidité extrême qui laisse place à un goût fruité délicieux. Un défi gustatif pour les amateurs de sensations fortes !',
                'marque' => 'Verquin',
                'category' => 'bonbons-acides',
                'featured' => true,
                'tags' => ['vegan'],
                'supplier' => $lutti,
                'variants' => [
                    ['nom' => 'Sachet 100g', 'ht' => 1.42, 'ttc' => 1.50, 'poids' => 100, 'stock' => 200],
                    ['nom' => 'Sachet 250g', 'ht' => 2.37, 'ttc' => 2.50, 'poids' => 250, 'stock' => 100],
                    ['nom' => 'Boîte 500g', 'ht' => 4.27, 'ttc' => 4.50, 'poids' => 500, 'stock' => 50],
                ],
                'nutrition' => ['energie_kcal' => 390, 'energie_kj' => 1655, 'matieres_grasses' => 0.0, 'dont_acides_gras_satures' => 0.0, 'glucides' => 97, 'dont_sucres' => 75, 'proteines' => 0.0, 'ingredients' => 'Sucre, acidifiants (acide citrique, acide malique), arômes, colorants'],
            ],
            [
                'name' => 'Sucettes Chupa Chups',
                'description_courte' => 'Les sucettes les plus célèbres du monde, multiples parfums.',
                'description_longue' => 'Chupa Chups, créée en 1958, propose des sucettes rondes aux parfums variés : fraise, cola, orange, cerise. Un classique de la confiserie mondiale.',
                'marque' => 'Chupa Chups',
                'category' => 'sucettes',
                'featured' => true,
                'tags' => ['sans-gluten'],
                'supplier' => $lutti,
                'variants' => [
                    ['nom' => 'Unité', 'ht' => 0.47, 'ttc' => 0.50, 'poids' => 12, 'stock' => 500],
                    ['nom' => 'Lot de 10', 'ht' => 3.79, 'ttc' => 4.00, 'poids' => 120, 'stock' => 80],
                    ['nom' => 'Pot de 50', 'ht' => 16.11, 'ttc' => 17.00, 'poids' => 600, 'stock' => 20],
                ],
                'nutrition' => ['energie_kcal' => 388, 'energie_kj' => 1648, 'matieres_grasses' => 0.0, 'dont_acides_gras_satures' => 0.0, 'glucides' => 97, 'dont_sucres' => 82, 'proteines' => 0.0, 'ingredients' => 'Sucre, sirop de glucose, acidifiant, arômes, colorants'],
            ],
            [
                'name' => 'Rouleaux de Réglisse',
                'description_courte' => 'Rouleaux de réglisse noire traditionnelle au goût intense.',
                'description_longue' => 'Les Rouleaux de Réglisse sont fabriqués à partir d\'extrait de réglisse véritable. Leur goût caractéristique et leur forme enroulée en font un classique apprécié des connaisseurs.',
                'marque' => 'Haribo',
                'category' => 'reglisse',
                'featured' => false,
                'tags' => ['vegan'],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.80, 'ttc' => 1.90, 'poids' => 100, 'stock' => 90],
                    ['nom' => '300g', 'ht' => 4.27, 'ttc' => 4.50, 'poids' => 300, 'stock' => 35],
                ],
                'nutrition' => ['energie_kcal' => 313, 'energie_kj' => 1328, 'matieres_grasses' => 2.0, 'dont_acides_gras_satures' => 0.4, 'glucides' => 70, 'dont_sucres' => 40, 'proteines' => 3.5, 'ingredients' => 'Sirop de glucose, farine de blé, sucre, extrait de réglisse, amidon', 'allergenes' => 'Contient du gluten'],
            ],
            [
                'name' => 'Bonbons Schtroumpfs',
                'description_courte' => 'Les fameux Schtroumpfs bleus et blancs gélifiés Haribo.',
                'description_longue' => 'Les bonbons Schtroumpfs sont des gélifiés en forme du célèbre personnage de BD. Avec leur double saveur et leur couleur bleue iconique, ils sont irrésistibles.',
                'marque' => 'Haribo',
                'category' => 'bonbons-classiques',
                'featured' => true,
                'tags' => [],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.71, 'ttc' => 1.80, 'poids' => 100, 'stock' => 100],
                    ['nom' => '250g', 'ht' => 3.03, 'ttc' => 3.20, 'poids' => 250, 'stock' => 55],
                ],
                'nutrition' => ['energie_kcal' => 343, 'energie_kj' => 1456, 'matieres_grasses' => 0.4, 'dont_acides_gras_satures' => 0.1, 'glucides' => 77, 'dont_sucres' => 55, 'proteines' => 6.2, 'ingredients' => 'Sirop de glucose, sucre, gélatine, amidon, arômes, colorants (bleu patenté V)'],
            ],
            [
                'name' => 'Chamallows Géants',
                'description_courte' => 'Enormes guimauves moelleuses bicolores rose et blanc.',
                'description_longue' => 'Les Chamallows Géants sont des guimauves XXL ultra-moelleuses. Parfaits pour le goûter, les barbecues ou simplement pour le plaisir de croquer dans un nuage sucré.',
                'marque' => 'Haribo',
                'category' => 'guimauves',
                'featured' => true,
                'tags' => ['sans-gluten', 'nouveauté'],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '150g', 'ht' => 2.84, 'ttc' => 3.00, 'poids' => 150, 'stock' => 60],
                    ['nom' => '400g', 'ht' => 5.21, 'ttc' => 5.50, 'poids' => 400, 'stock' => 25],
                ],
                'nutrition' => ['energie_kcal' => 330, 'energie_kj' => 1400, 'matieres_grasses' => 0.1, 'dont_acides_gras_satures' => 0.0, 'glucides' => 80, 'dont_sucres' => 60, 'proteines' => 3.0, 'ingredients' => 'Sucre, sirop de glucose, gélatine, amidon, arômes, colorants'],
            ],
            [
                'name' => 'Chocolat Kinder',
                'description_courte' => 'Barre de chocolat au lait fourrée au lait, pour les petits et grands.',
                'description_longue' => 'Le Kinder Chocolat combine une fine couche de chocolat au lait avec un coeur de crème au lait. Un goûter équilibré et gourmand apprécié dans le monde entier.',
                'marque' => 'Kinder',
                'category' => 'chocolats',
                'featured' => true,
                'tags' => [],
                'supplier' => $lutti,
                'variants' => [
                    ['nom' => 'Barre unitaire', 'ht' => 0.95, 'ttc' => 1.00, 'poids' => 21, 'stock' => 200],
                    ['nom' => 'Pack de 4', 'ht' => 2.84, 'ttc' => 3.00, 'poids' => 84, 'stock' => 100],
                    ['nom' => 'Pack de 8', 'ht' => 4.74, 'ttc' => 5.00, 'poids' => 168, 'stock' => 60],
                ],
                'nutrition' => ['energie_kcal' => 567, 'energie_kj' => 2372, 'matieres_grasses' => 35, 'dont_acides_gras_satures' => 22, 'glucides' => 52, 'dont_sucres' => 51, 'proteines' => 9.5, 'ingredients' => 'Chocolat au lait 40% (sucre, beurre de cacao, lait en poudre, pâte de cacao), lait écrémé en poudre, sucre, huile de palme, beurre concentré', 'allergenes' => 'Lait, soja. Peut contenir des traces de noisettes et blé'],
            ],
            [
                'name' => 'Fizzy Cola',
                'description_courte' => 'Bouteilles de cola pétillantes et acidulées.',
                'description_longue' => 'Les Fizzy Cola sont des bonbons gélifiés en forme de bouteilles de cola. Leur texture douce et leur goût de cola relevé d\'une touche acide en font un incontournable.',
                'marque' => 'Haribo',
                'category' => 'bonbons-acides',
                'featured' => false,
                'tags' => ['promo'],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.52, 'ttc' => 1.60, 'poids' => 100, 'stock' => 130],
                    ['nom' => '250g', 'ht' => 2.84, 'ttc' => 3.00, 'poids' => 250, 'stock' => 70],
                ],
                'nutrition' => ['energie_kcal' => 343, 'energie_kj' => 1456, 'matieres_grasses' => 0.4, 'dont_acides_gras_satures' => 0.1, 'glucides' => 77, 'dont_sucres' => 55, 'proteines' => 6.2, 'ingredients' => 'Sirop de glucose, sucre, gélatine, acidifiant, arôme cola, colorant (caramel)'],
            ],
            [
                'name' => 'Lacets Acidulés',
                'description_courte' => 'Longs lacets colorés au goût fruité et acidulé.',
                'description_longue' => 'Les Lacets Acidulés sont des confiseries longues et souples aux saveurs fruitées intenses. Parfaits pour les amateurs d\'acidité qui aiment jouer avec leur nourriture.',
                'marque' => 'Lutti',
                'category' => 'bonbons-acides',
                'featured' => false,
                'tags' => ['vegan'],
                'supplier' => $lutti,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.14, 'ttc' => 1.20, 'poids' => 100, 'stock' => 110],
                    ['nom' => '250g', 'ht' => 2.37, 'ttc' => 2.50, 'poids' => 250, 'stock' => 55],
                ],
                'nutrition' => ['energie_kcal' => 340, 'energie_kj' => 1440, 'matieres_grasses' => 0.2, 'dont_acides_gras_satures' => 0.0, 'glucides' => 80, 'dont_sucres' => 50, 'proteines' => 2.0, 'ingredients' => 'Sucre, sirop de glucose, farine de blé, acidifiants, arômes, colorants', 'allergenes' => 'Contient du gluten'],
            ],
            [
                'name' => 'Bananes Candy',
                'description_courte' => 'Bonbons tendres en mousse au goût banane.',
                'description_longue' => 'Les Bananes Candy sont des bonbons en mousse au goût intense de banane. Leur texture unique mi-mousse mi-gélifié et leur saveur fruitée en font un classique des confiseries.',
                'marque' => 'Haribo',
                'category' => 'bonbons-classiques',
                'featured' => false,
                'tags' => ['sans-gluten'],
                'supplier' => $haribo,
                'variants' => [
                    ['nom' => '100g', 'ht' => 1.52, 'ttc' => 1.60, 'poids' => 100, 'stock' => 85],
                    ['nom' => '250g', 'ht' => 2.65, 'ttc' => 2.80, 'poids' => 250, 'stock' => 40],
                ],
                'nutrition' => ['energie_kcal' => 350, 'energie_kj' => 1485, 'matieres_grasses' => 0.3, 'dont_acides_gras_satures' => 0.1, 'glucides' => 82, 'dont_sucres' => 58, 'proteines' => 4.0, 'ingredients' => 'Sucre, sirop de glucose, gélatine, amidon, arôme banane, colorants (curcumine)'],
            ],
        ];

        foreach ($productsData as $data) {
            $slug = Str::slug($data['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description_longue'],
                    'description_courte' => $data['description_courte'],
                    'description_longue' => $data['description_longue'],
                    'marque' => $data['marque'],
                    'price' => $data['variants'][0]['ttc'],
                    'stock' => 0,
                    'category_id' => ($cats[$data['category']] ?? Category::where('slug', $data['category'])->first())->id,
                    'featured' => $data['featured'],
                    'actif' => true,
                    'meta_title' => $data['name'] . ' | Chatpito.fr',
                    'meta_description' => $data['description_courte'],
                ]
            );

            // Supprimer les anciennes variantes LEGACY
            $product->variants()->where('sku', 'like', 'LEGACY-%')->delete();

            // Créer les variantes
            foreach ($data['variants'] as $v) {
                $sku = strtoupper(Str::slug($data['marque'] ?? 'GEN', '-')) . '-' . strtoupper(Str::slug($data['name'], '-')) . '-' . strtoupper(Str::slug($v['nom'], '-'));

                $variant = Variant::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'produit_id' => $product->id,
                        'nom' => $v['nom'],
                        'prix_vente_ht' => $v['ht'],
                        'prix_vente_ttc' => $v['ttc'],
                        'taux_tva' => $v['tva'] ?? 5.50,
                        'poids' => $v['poids'],
                        'actif' => true,
                    ]
                );

                Stock::updateOrCreate(
                    ['variant_id' => $variant->id],
                    [
                        'quantite_disponible' => $v['stock'],
                        'quantite_physique' => $v['stock'],
                        'quantite_reservee' => 0,
                        'seuil_alerte' => 10,
                        'seuil_reappro' => 20,
                    ]
                );

                // Mouvement d'entrée initial
                StockMovement::updateOrCreate(
                    ['variant_id' => $variant->id, 'type' => 'entree', 'commentaire' => 'Stock initial'],
                    [
                        'quantite' => $v['stock'],
                        'stock_avant' => 0,
                        'stock_apres' => $v['stock'],
                    ]
                );

                // Lien fournisseur
                $variant->suppliers()->syncWithoutDetaching([
                    $data['supplier']->id => [
                        'prix_achat_ht' => round($v['ht'] * 0.6, 2),
                        'taux_tva_achat' => 5.50,
                        'prix_achat_ttc' => round($v['ht'] * 0.6 * 1.055, 2),
                        'fournisseur_sku' => 'F-' . $sku,
                        'quantite_min_commande' => 10,
                        'multiple_commande' => 10,
                        'delai_livraison_jours' => rand(3, 7),
                        'actif' => true,
                    ],
                ]);
            }

            // Tags
            if (!empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagSlug) {
                    if (isset($tags[$tagSlug])) {
                        $tagIds[] = $tags[$tagSlug]->id;
                    }
                }
                $product->tags()->syncWithoutDetaching($tagIds);
            }

            // Infos nutritionnelles
            if (!empty($data['nutrition'])) {
                NutritionalInfo::updateOrCreate(
                    ['produit_id' => $product->id],
                    $data['nutrition']
                );
            }
        }
    }
}
