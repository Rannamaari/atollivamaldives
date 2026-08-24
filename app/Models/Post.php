<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable=['title','slug','category','excerpt','body','featured_image','author','published','featured','published_at','seo_title','seo_description'];
    protected function casts(): array { return ['published'=>'boolean','featured'=>'boolean','published_at'=>'datetime']; }
    public function scopePublished(Builder $query): Builder { return $query->where('published',true)->where(fn($q)=>$q->whereNull('published_at')->orWhere('published_at','<=',now())); }
    public function getRouteKeyName(): string { return 'slug'; }
}
