<?php

namespace App\Http\Controllers\Dashboard;

use App\Company;
use App\Cards;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Imports\CardImport;
use DB;
use App\Anaiscodes;
use App\Order;
use App\Carbon\Carbon;
use App\Exports\AnisCodesex;
use Maatwebsite\Excel\Facades\Excel;
class AnisCodes extends Controller
{
    public function index(Request $request)
    {
        $Cards = Anaiscodes::with('orders')->when($request->search, function ($q) use ($request) {

            return $q->where('order_id',  'like', '%' . $request->search . '%');

        })->paginate(20);
        $Companies = Company::all();
   // dd($Cards);
   
     if (isset($request->print) ){
            return Excel::download(new Anaiscodes($Cards), 'Anis Sold cards.xlsx');
         }
         
        return view('dashboard.AnisCodes.index', compact('Cards','Companies'));

    }//end of index






}//end of controller
