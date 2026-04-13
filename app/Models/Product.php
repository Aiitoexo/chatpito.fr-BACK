<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'description_courte', 'description_longue',
        'marque', 'price', 'image', 'stock', 'featured', 'actif',
        'meta_title', 'meta_description', 'category_id', 'tax_rate_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'actif' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'produit_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'produit_id');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'produit_id')->where('est_principale', true);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'produit_id', 'tag_id');
    }

    public function nutritionalInfo(): HasOne
    {
        return $this->hasOne(NutritionalInfo::class, 'produit_id');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
