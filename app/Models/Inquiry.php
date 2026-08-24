<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;
    protected $fillable=['name','email','phone','travel_type','travel_date','travellers','budget','message','status','source'];
    protected function casts(): array { return ['travel_date'=>'date','travellers'=>'integer']; }
}
