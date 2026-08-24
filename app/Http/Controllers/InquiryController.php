<?php
namespace App\Http\Controllers;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data=$request->validate(['name'=>'required|string|max:100','phone'=>'required|string|max:30','email'=>'nullable|email|max:150','travel_type'=>'nullable|string|max:30','travel_date'=>'nullable|date','travellers'=>'nullable|integer|min:1|max:50','budget'=>'nullable|string|max:100','message'=>'nullable|string|max:3000']);
        Inquiry::create($data+['source'=>'website','status'=>'new']);
        return back()->with('success','Thank you. A Micro Travel expert will contact you soon.');
    }
}
