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
use Maatwebsite\Excel\Facades\Excel;
use DB;

use App\productorders;
use App\Carbon\Carbon;
class ProductsOrdersController extends Controller
{
    public function index(Request $request)
    {



        $orders =productorders::orderBy('id', 'DESC')->get();
        
      //  DB::connection('products')->select("select * from order_details");
        
    // dd($orders);

        return view('dashboard.productsorders.index', compact('orders'));

    }//end of index

    public function create()
    {
         $Companies = DB::connection('products')->select("select * from company");
        return view('dashboard.productsnew.create', compact('Companies'));

    }//end of create

    public function store(Request $request)
    {

            $rules = [
                'name' => 'required',
                'price' => 'required',
                'amount' => 'required',
                'company_id'>'required'
            ];
            DB::connection('products')->table('product')->insert(
    ['name' =>$request->name,'price' =>$request->price,'amount' =>$request->amount, 'isactive' => 1, 'isavilable' => 1,'type'=>'Product',
    'companyid'=>$request->company_id, 'code'=>$request->code]
);

                session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.products.index');
    }//end of store




    public function edit($id )
    {
        $Companies =  DB::connection('products')->select("select * from company");
        $category= DB::connection('products')->select("select * from product where id = '$id'");
      //dd($category);
        return view('dashboard.productsnew.edit', compact('category','Companies'));

    }//end of edit

    public function update(Request $request,$id)
    {
        $category=DB::connection('products')->select("select * from product where id = '$id'");


        $request_data = $request->except(['_token', '_method']);
        
     
  
           DB::connection('products')->table('product')->where('id',$id)->update(
    ['name' =>$request->name,'price' =>$request->price,'amount' =>$request->amount, 'isactive' => 1, 'isavilable' => 1,'type'=>'Product',
    'companyid'=>$request->company_id, 'code'=>$request->code]
);


       
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.products.index');

    }//end of update

    public function destroy(Request $request) 
    {
      
        
              DB::connection('products')->table('product')->where('id',$request->card_id)->delete();
  
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.products.index');

    }//end of destroy


public function productsorderdetails($id){
  //  dd($id);
    $orders=DB::connection('products')->select("select * from order_details where orderid='$id'");
     //dd($orders);
    
        return view('dashboard.productsorders.indexdetails', compact('orders'));
}


}//end of controller
