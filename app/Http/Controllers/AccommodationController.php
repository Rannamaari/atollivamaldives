<?php
namespace App\Http\Controllers;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(Request $request): View
    {
        $items=Accommodation::published()->when($request->type,fn($q,$type)=>$q->where('type',$type))->when($request->atoll,fn($q,$atoll)=>$q->where('atoll',$atoll))->orderBy('sort_order')->paginate(12)->withQueryString();
        return view('accommodations.index',compact('items'));
    }
    public function show(Accommodation $accommodation): View
    {
        abort_unless($accommodation->published,404);
        return view('accommodations.show',compact('accommodation'));
    }
}
