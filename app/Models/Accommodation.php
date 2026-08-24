<?php
namespace App\Models;
use App\Enums\AccommodationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    use HasFactory;
    protected $fillable = ['type','name','slug','tagline','summary','description','island','atoll','address','price_from','currency','price_unit','rating','images','amenities','featured','published','sort_order','seo_title','seo_description'];
    protected function casts(): array { return ['type'=>AccommodationType::class,'images'=>'array','amenities'=>'array','featured'=>'boolean','published'=>'boolean','price_from'=>'decimal:2','rating'=>'decimal:1']; }
    public function scopePublished(Builder $query): Builder { return $query->where('published',true); }
    public function getRouteKeyName(): string { return 'slug'; }
    public function getCoverImageAttribute(): string { return $this->images[0] ?? 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1400&q=85'; }
}
