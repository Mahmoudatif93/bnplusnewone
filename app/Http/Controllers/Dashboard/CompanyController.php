<?php

namespace App\Http\Controllers\Dashboard;

use App\Company;
use App\Cards;
use App\Anaiscodes;
use App\cards_anais;
use App\Order;
use App\Client;
use App\Order_anais;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PDF2;
use App\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DB;
use App\currancylocal;
class CompanyController extends Controller
{

    /* function generateHash($time){
        $email = strtolower('merchant-email@domain.com');
        $phone = '966577753100';
        $key = '******';
        return hash('sha256',$time.$email.$phone.$key);
      }*/








public function testanis(Request $request){
    
     $uri = 'https://identity.anis.ly/connect/token';
        $params = array(
            'grant_type' => 'user_credentials',
            'client_id' => 'bn-plus',
            'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
            'password' => 'P@ssw0rd1988',
            'email' => 'info@bn-plus.ly',
        );
        $response = Http::asForm()->withHeaders([])->post($uri, $params);
        $token = $response->json()['access_token'];
        $token_type = $response->json()['token_type'];
        $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

        $orderswal = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $alltoken,

        ])->get(
            'https://gateway.anis.ly/api/consumers/v1/transactions/E1521F1F-C592-42F3-7A1A-08D9F31F6661/current-balance'


        );




        if ($orderswal->json()['data'] > 0) {


            $uri = 'https://identity.anis.ly/connect/token';
            $params = array(
                'grant_type' => 'user_credentials',
                'client_id' => 'bn-plus',
                'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                'password' => 'P@ssw0rd1988',
                'email' => 'info@bn-plus.ly',
            );
            $response = Http::asForm()->withHeaders([])->post($uri, $params);
            $token = $response->json()['access_token'];
            $token_type = $response->json()['token_type'];
            $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];








            $swaggercompanies = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->get('https://gateway.anis.ly/api/consumers/v1/categories', []);
            //  dd($swaggercompanies->json()['data']);echo"<br>";

            if (!empty($swaggercompanies->json()['data'])) {
                $competition_all = array();
                foreach ($swaggercompanies->json()['data'] as $rowcomp) {
                    if ($rowcomp['type'] == 'Local' ) {
                        if (!empty($rowcomp['subCategories'])) {

                            foreach ($rowcomp['subCategories'] as $rowsubcomp) {


                                array_push($competition_all, $rowsubcomp['id']);


                                if ($rowsubcomp['inStock'] == true) {
                                    $itemcomp = Company::firstOrNew(array('idapi2' => $rowsubcomp['id']));

                                    $itemcomp->idapi2 = $rowsubcomp['id'];
                                    $itemcomp->company_image =  $rowsubcomp['logo'];
                                    $itemcomp->name = $rowsubcomp['name'];
                                    $itemcomp->kind = 'local';
                                    $itemcomp->api2 = 1;
                                    $itemcomp->save();









                                    /////////////////////cards1
                                    $uri = 'https://identity.anis.ly/connect/token';
                                    $params = array(
                                        'grant_type' => 'user_credentials',
                                        'client_id' => 'bn-plus',
                                        'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                        'password' => 'P@ssw0rd1988',
                                        'email' => 'info@bn-plus.ly',
                                    );
                                    $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                    $token = $response->json()['access_token'];
                                    $token_type = $response->json()['token_type'];
                                    $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                    $compid = $rowsubcomp['id'];


                                    $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';

                                    $cards = Http::withHeaders([
                                        'Accept' => 'application/json',
                                        'Authorization' => $alltoken,

                                    ])->get($compurl);



                                    if (!empty($cards->json()['data']['cards'])) {

                                        foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                            $dbCompanies = Company::where(array('api2' => 1, 'idapi2' => $compid))->first();
                                            // print_r($dbCompanies);echo"<br>";
                                            //print_r($cardsapi['name']);echo"<br>";
                                            if (!empty($dbCompanies)) {
                                                $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                                //print_r($cardsapi['name']);
                                               // echo "<br>";
                                                if ($itemcard->old_price != $cardsapi['businessPrice']) {
                                                    $itemcard->api2id = $cardsapi['id'];
                                                    $itemcard->old_price = $cardsapi['businessPrice'];
                                                    $itemcard->company_id = $dbCompanies->id;
                                                    $itemcard->card_name = $cardsapi['name'];
                                                    $itemcard->card_price = $cardsapi['businessPrice'];
                                                    $itemcard->card_code = $cardsapi['name'];
                                                    $itemcard->card_image = $cardsapi['logo'];
                                                    $itemcard->nationalcompany =  'local';
                                                    $itemcard->api2 = 1;
                                                    $itemcard->purchase = 0;

                                                    //   dd($itemcard);
                                                    $itemcard->save();
                                                }

                                                if ($cardsapi['inStock'] == true) {

                                                    $itemcard->api2id = $cardsapi['id'];

                                                    $itemcard->company_id = $dbCompanies->id;
                                                    $itemcard->card_name = $cardsapi['name'];

                                                    $itemcard->card_code = $cardsapi['name'];
                                                    $itemcard->card_image = $cardsapi['logo'];
                                                    $itemcard->nationalcompany =  'local';
                                                    $itemcard->api2 = 1;
                                                    $itemcard->purchase = 0;

                                                    //   dd($itemcard);
                                                    $itemcard->save();
                                                }
                                            }
                                        }
                                    }

                                    /////////////////////////////












                                } else {

                                    $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                    if (!empty($allcomsp)) {
                                        foreach ($allcomsp as $compa) {
                                            $allcardsq = Cards::where('company_id', $compa->id)->get();
                                            if (!empty($allcardsq)) {
                                                foreach ($allcardsq as $allcarss) {

                                                    if ($allcarss->api2 == 1) {
                                                        $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                        $cardschek = Http::withHeaders([
                                                            'Accept' => 'application/json',
                                                            'Authorization' => $alltoken,

                                                        ])->get($compurlcheck);

                                                        if (isset($cardschek->json()['data'])) {

                                                            if ($cardschek->json()['data']['inStock'] == false) {
                                                                $updatecard['purchase'] = 1;
                                                                $updatecard['avaliable'] = 1;
                                                                Cards::where('id', $allcarss->id)->update($updatecard);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($rowcomp['inStock'] == true) {
                                $itemcomp = Company::firstOrNew(array('idapi2' => $rowcomp['id']));

                                $itemcomp->idapi2 = $rowcomp['id'];
                                $itemcomp->company_image =  $rowcomp['logo'];
                                $itemcomp->name = $rowcomp['name'];
                                $itemcomp->kind = 'local';
                                $itemcomp->api2 = 1;
                                $itemcomp->save();
















                                /////////////////////cards2
                                $uri = 'https://identity.anis.ly/connect/token';
                                $params = array(
                                    'grant_type' => 'user_credentials',
                                    'client_id' => 'bn-plus',
                                    'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                    'password' => 'P@ssw0rd1988',
                                    'email' => 'info@bn-plus.ly',
                                );
                                $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                $token = $response->json()['access_token'];
                                $token_type = $response->json()['token_type'];
                                $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                $compid = $rowcomp['id'];

                                $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';


                                $cards = Http::withHeaders([
                                    'Accept' => 'application/json',
                                    'Authorization' => $alltoken,

                                ])->get($compurl);

                                if (!empty($cards->json()['data']['cards'])) {

                                    foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                        $dbCompanies = Company::where(array('api2' => 1, 'idapi2' => $compid))->first();
                                       // print_r($cardsapi['name']);
                                       // echo "<br>";
                                        if (!empty($dbCompanies)) {
                                            $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                            if ($itemcard->old_price != $cardsapi['businessPrice']) {
                                                $itemcard->api2id = $cardsapi['id'];
                                                $itemcard->old_price = $cardsapi['businessPrice'];
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                                $itemcard->card_price = $cardsapi['businessPrice'];
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'local';
                                                $itemcard->api2 = 1;
                                                //   dd($itemcard);
                                                $itemcard->purchase = 0;
                                                $itemcard->save();
                                            }

                                            if ($cardsapi['inStock'] == true) {

                                                $itemcard->api2id = $cardsapi['id'];

                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];

                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'local';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase = 0;

                                                //   dd($itemcard);
                                                $itemcard->save();
                                            }
                                        }
                                    }
                                }

                                /////////////////////////////
























                            } else {

                                $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                if (!empty($allcomsp)) {
                                    foreach ($allcomsp as $compa) {
                                        $allcardsq = Cards::where('company_id', $compa->id)->get();
                                        if (!empty($allcardsq)) {
                                            foreach ($allcardsq as $allcarss) {

                                                if ($allcarss->api2 == 1) {
                                                    $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                    $cardschek = Http::withHeaders([
                                                        'Accept' => 'application/json',
                                                        'Authorization' => $alltoken,

                                                    ])->get($compurlcheck);

                                                    if (isset($cardschek->json()['data'])) {

                                                        if ($cardschek->json()['data']['inStock'] == false) {
                                                            $updatecard['purchase'] = 1;
                                                            $updatecard['avaliable'] = 1;
                                                            Cards::where('id', $allcarss->id)->update($updatecard);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {


//print_r($rowcomp['subCategories']
print_r($cardsapi['name']);
                                              echo "<br>";
                        if (!empty($rowcomp['subCategories'])) {
                            foreach ($rowcomp['subCategories'] as $rowsubcomp) {
                                if ($rowsubcomp['inStock'] == true) {
                                    $itemcomp = Company::firstOrNew(array('idapi2' => $rowsubcomp['id']));

                                    $itemcomp->idapi2 = $rowsubcomp['id'];
                                    $itemcomp->company_image =  $rowsubcomp['logo'];
                                    $itemcomp->name = $rowsubcomp['name'];
                                    $itemcomp->kind = 'national';
                                    $itemcomp->api2 = 1;
                                    $itemcomp->save();
                                    /////////////////////cards3
                                    $uri = 'https://identity.anis.ly/connect/token';
                                    $params = array(
                                        'grant_type' => 'user_credentials',
                                        'client_id' => 'bn-plus',
                                        'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                        'password' => 'P@ssw0rd1988',
                                        'email' => 'info@bn-plus.ly',
                                    );
                                    $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                    $token = $response->json()['access_token'];
                                    $token_type = $response->json()['token_type'];
                                    $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                    $compid = $rowsubcomp['id'];

                                    $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';

                                    $cards = Http::withHeaders([
                                        'Accept' => 'application/json',
                                        'Authorization' => $alltoken,

                                    ])->get($compurl);

                                    if (!empty($cards->json()['data']['cards'])) {

                                        foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                            $dbCompanies = Company::where(array('api2' => 1, 'idapi2' => $compid))->first();
                                         //   print_r($cardsapi['name']);
                                          //  echo "<br>";
                                            $curr =  currancylocal::first();
                                            if (!empty($dbCompanies)) {
                                                $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                                if ($itemcard->old_price != $cardsapi['businessPrice']) {
                                                    $itemcard->api2id = $cardsapi['id'];
                                                    $itemcard->old_price = $cardsapi['businessPrice'];
                                                    $itemcard->company_id = $dbCompanies->id;
                                                    $itemcard->card_name = $cardsapi['name'];
                                                    $itemcard->card_price = round((($cardsapi['businessPrice']  * $curr->amount) / 100) + $cardsapi['businessPrice'], 3);
                                                    $itemcard->card_code = $cardsapi['name'];
                                                    $itemcard->card_image = $cardsapi['logo'];
                                                    $itemcard->nationalcompany =  'national';
                                                    $itemcard->api2 = 1;
                                                    //   dd($itemcard);
                                                    $itemcard->purchase = 0;
                                                    $itemcard->save();
                                                }

                                                if ($cardsapi['inStock'] == true) {

                                                    $itemcard->api2id = $cardsapi['id'];

                                                    $itemcard->company_id = $dbCompanies->id;
                                                    $itemcard->card_name = $cardsapi['name'];

                                                    $itemcard->card_code = $cardsapi['name'];
                                                    $itemcard->card_image = $cardsapi['logo'];
                                                    $itemcard->nationalcompany =  'national';
                                                    $itemcard->api2 = 1;
                                                    $itemcard->purchase = 0;

                                                    //   dd($itemcard);
                                                    $itemcard->save();
                                                }
                                            }
                                        }
                                    }

                                    /////////////////////////////









                                } else {

                                    $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                    if (!empty($allcomsp)) {
                                        foreach ($allcomsp as $compa) {
                                            $allcardsq = Cards::where('company_id', $compa->id)->get();
                                            if (!empty($allcardsq)) {
                                                foreach ($allcardsq as $allcarss) {

                                                    if ($allcarss->api2 == 1) {
                                                        $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                        $cardschek = Http::withHeaders([
                                                            'Accept' => 'application/json',
                                                            'Authorization' => $alltoken,

                                                        ])->get($compurlcheck);

                                                        if (isset($cardschek->json()['data'])) {

                                                            if ($cardschek->json()['data']['inStock'] == false) {
                                                                $updatecard['purchase'] = 1;
                                                                $updatecard['avaliable'] = 1;
                                                                Cards::where('id', $allcarss->id)->update($updatecard);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($rowcomp['inStock'] == true) {
                                $itemcomp = Company::firstOrNew(array('idapi2' => $rowcomp['id']));

                                $itemcomp->idapi2 = $rowcomp['id'];
                                $itemcomp->company_image =  $rowcomp['logo'];
                                $itemcomp->name = $rowcomp['name'];
                                $itemcomp->kind = 'national';
                                $itemcomp->api2 = 1;
                                $itemcomp->save();






                                /////////////////////cards4
                                $uri = 'https://identity.anis.ly/connect/token';
                                $params = array(
                                    'grant_type' => 'user_credentials',
                                    'client_id' => 'bn-plus',
                                    'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                    'password' => 'P@ssw0rd1988',
                                    'email' => 'info@bn-plus.ly',
                                );
                                $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                $token = $response->json()['access_token'];
                                $token_type = $response->json()['token_type'];
                                $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                $compid = $rowcomp['id'];

                                $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';

                                $cards = Http::withHeaders([
                                    'Accept' => 'application/json',
                                    'Authorization' => $alltoken,

                                ])->get($compurl);


                                if (!empty($cards->json()['data']['cards'])) {

                                    foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                        $dbCompanies = Company::where(array('api2' => 1, 'idapi2' => $compid))->first();
                                       // print_r($cardsapi['name']);
                                     //   echo "<br>";
                                        $curr =  currancylocal::first();
                                        if (!empty($dbCompanies)) {
                                            $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                            if ($itemcard->old_price != $cardsapi['businessPrice']) {


                                                $itemcard->api2id = $cardsapi['id'];
                                                $itemcard->old_price = $cardsapi['businessPrice'];
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                                $itemcard->card_price =  round((($cardsapi['businessPrice']  * $curr->amount) / 100), 3) + $cardsapi['businessPrice'];
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'national';
                                                $itemcard->api2 = 1;
                                                //   dd($itemcard);
                                                $itemcard->purchase = 0;
                                                $itemcard->save();
                                            }

                                            if ($cardsapi['inStock'] == true) {

                                                $itemcard->api2id = $cardsapi['id'];

                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];

                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'national';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase = 0;

                                                //   dd($itemcard);
                                                $itemcard->save();
                                            }
                                        }
                                    }
                                }

                                /////////////////////////////


















                            } else {

                                $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                if (!empty($allcomsp)) {
                                    foreach ($allcomsp as $compa) {
                                        $allcardsq = Cards::where('company_id', $compa->id)->get();
                                        if (!empty($allcardsq)) {
                                            foreach ($allcardsq as $allcarss) {

                                                if ($allcarss->api2 == 1) {
                                                    $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                    $cardschek = Http::withHeaders([
                                                        'Accept' => 'application/json',
                                                        'Authorization' => $alltoken,

                                                    ])->get($compurlcheck);

                                                    if (isset($cardschek->json()['data'])) {

                                                        if ($cardschek->json()['data']['inStock'] == false) {
                                                            $updatecard['purchase'] = 1;
                                                            $updatecard['avaliable'] = 1;
                                                            Cards::where('id', $allcarss->id)->update($updatecard);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    
}


    public function index(Request $request)
    {

//dd(public_path());

  /*$this->testanis($request);
     return;*/
     


        ini_set("prce.backtrack_limit", "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");




        $Companies = Company::where('enable', 0)->when($request->search, function ($q) use ($request) {

            return $q->where('name', 'like', '%' .  $request->search . '%')
                ->orWhere('kind', 'like', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.Companies.index', compact('Companies'));
    } //end of index

    public function create()
    {
        return view('dashboard.Companies.create');
    } //end of create

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'kind' => 'required',
        ];

        $request->validate($rules);
        $request_data = $request->all();
        if ($request->file('company_image')) {
         $nassme = $request->file('company_image')->getClientOriginalName();
//dd(public_path('uploads/company/' . $nassme));

          $dd= /*  Image::make($request->file('company_image'))
               /* ->resize(100, null, function ($constraint) {
                    $constraint->aspectRatio();
                })*/
              
                $request->file('company_image')->move(public_path('uploads/company/' . $nassme));
 
            $request_data['company_image'] = 'https://bn-plus.ly/BNplus/public/uploads/company/' . $nassme;
        } //end of if

        Company::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of store

    public function edit($id)
    {
        $category = Company::where('id', $id)->first();
        return view('dashboard.Companies.edit', compact('category'));
    } //end of edit

    public function update(Request $request, $id)
    {
        $category = Company::where('id', $id)->first();


        $request_data = $request->except(['_token', '_method']);
        if ($request->company_image) {

            if ($category->company_image != '') {

                Storage::disk('public_uploads')->delete('/company/' . $category->company_image);
            } //end of if

            Image::make($request->company_image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/company/' . $request->company_image->hashName()));

            $request_data['company_image'] = 'https://bn-plus.ly/BNplus/public/uploads/company/' . $request->company_image->hashName();
        } //end of if



        Company::where('id', $id)->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of update

    public function destroy($id)
    {
        $category = Company::where('id', $id)->first();
        if ($category->company_image != '') {

            Storage::disk('public_uploads')->delete('/company/' . $category->company_image);
        } //end of if

        Company::where('id', $id)->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of destroy


    function generate_pdf()
    {
        $data = [
            'foo' => 'bar'
        ];
        $pdf = PDF2::loadView('dashboard.Companies.pdf', $data);
        return $pdf->stream('document.pdf');
    }

    public function sendResetEmail($user, $content, $subject)
    {

        $send =   Mail::send(
            'dashboard.Contacts.content',
            ['user' => $user, 'content' => $content, 'subject' => $subject],
            function ($message) use ($user, $subject) {
                $message->to($user);
                $message->subject("$subject");
            }
        );
    }
}//end of controller
