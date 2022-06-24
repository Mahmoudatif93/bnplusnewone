<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Order;
use App\Cards;
use App\Client;
use App\Anaiscodes;
use App\cards_anais;
use App\tdawalDetails;
use App\Order_anais;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PDF2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TadawalController extends Controller
{

    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function reservetadalwalorder(Request $request)
    {

        $cardscount = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->count();
        $card = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->orderBy('id', 'desc')->first();
        // return($cardscount);
        if ($cardscount > 0) {
            if ($card->api2 == 1) {
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

                $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $card->api2id . '';

                $cardschek = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => $alltoken,
                ])->get($compurlcheck);


                if (isset($cardschek->json()['data'])) {
                    if ($cardschek->json()['data']['inStock'] == false) {


                        $updatecard['purchase'] = 1;
                        $updatecard['avaliable'] = 1;
                        Cards::where('id', $card->id)->update($updatecard);
                        return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                    } else {

                        $request_data['card_id'] = $card->id;
                        $request_data['client_id'] = $request->client_id;
                        $request_data['card_price'] = $request->card_price;
                        $request_data['client_name'] = $request->client_name;
                        $request_data['client_number'] = $request->client_number;
                        $request_data['paymenttype'] = "تداول";
                        $order = Order::create($request_data);
                        if ($order) {
                            $message = "card reserved ";

                            return  $this->InitiatePaymenttly($request, $order->id);
                            return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
                        } else {
                            return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                        }
                    }
                }else{
                    return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                }
            } else {
                if ($card->api == 1) {
                    $request_data['card_id'] = $card->id;
                } else {
                    $request_data['card_id'] = $card->id;
                }
                $request_data['client_id'] = $request->client_id;
                $request_data['card_price'] = $request->card_price;
                $request_data['client_name'] = $request->client_name;
                $request_data['client_number'] = $request->client_number;
                $request_data['paymenttype'] = "تداول";
                $order = Order::create($request_data);
                if ($order) {
                    if ($card->api2 != 1) {
                        $dataa['avaliable'] = 1;

                        Cards::where('id', $order->card_id)->update($dataa);
                    }
                    $message = "card reserved ";
                    return   $this->InitiatePaymenttly($request, $order->id);
                    return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
                } else {

                    return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                }
            }
        } else {
            $message = "No Cards Avaliable For this Price";
            return $this->apiResponse6($cardscount, null, $message, 404);
        }
    }


    public function confirmorders($client_id, $order_id, $client_email, $client_name, $customer_phone, $transaction_id)
    {

        $order = Order::find($order_id);

        $dubiapi =  Cards::where('id', $order->card_id)->first();
        if (!empty($order)) {


            $is_expired = $order->created_at->addMinutes(5);
            if ($is_expired < \Carbon\Carbon::now()) {

                return response()->json(['status' => 'error']);
            } else {


                $order->transaction_id = $transaction_id;
                $order->paid = 'true';
                $order->paymenttype = "تداول";

                $allcompanyid = array();
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://taxes.like4app.com/online/check_balance/",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => array(
                        'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                        'email' => 'info@bn-plus.ly',
                        'password' => 'db7d8028631f3351731cf7ca0302651d',
                        'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                        'langId' => '1'
                    ),

                ));

                $balancenational = curl_exec($curl);


                $dubiapi =  Cards::where('id', $order->card_id)->first();
                $clientdata =  Client::where('id', $order->client_id)->first();
                if ($dubiapi->api != 1 || $dubiapi->api2 != 1) {


                    //  $this->InitiateTlync('1',$customer_phone,$client_email);
                    $cardsanaia = Cards::where('id', $order->card_id)->first();

                    $cardsanaia = Cards::where('id', $order->card_id)->first();
                    $Anaiscards['id'] = $cardsanaia->id;
                    /*$itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                    $itemcomp->id = $cardsanaia->id;
                    $itemcomp->order_id =  $order->id;
                    $itemcomp->card_name = $cardsanaia->card_name;
                    $itemcomp->company_id = $cardsanaia->company_id;
                    $itemcomp->api =  $cardsanaia->api;
                    $itemcomp->card_price =  $cardsanaia->card_price;
                    $itemcomp->card_code = $cardsanaia->card_code;
                    $itemcomp->amounts = $cardsanaia->amounts;
                    $itemcomp->avaliable = $cardsanaia->avaliable;
                    $itemcomp->purchase =  $cardsanaia->purchase;
                    $itemcomp->card_image = $cardsanaia->card_image;
                    $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                    $itemcomp->productId = $cardsanaia->productId;
                    $itemcomp->enable = $cardsanaia->enable;
                    $itemcomp->api2 = $cardsanaia->api2;
                    $itemcomp->api2id = $cardsanaia->api2id;
                    $itemcomp->save();*/


                    $itemcomp['id'] =  $cardsanaia->id;
                    $itemcomp['order_id'] =  $order->id;
                    $itemcomp['card_name'] = $cardsanaia->card_name;
                    $itemcomp['company_id'] = $cardsanaia->company_id;
                    $itemcomp['api'] = $cardsanaia->api;
                    $itemcomp['card_price'] = $cardsanaia->card_price;
                    $itemcomp['card_code'] = $cardsanaia->card_code;
                    $itemcomp['amounts'] = $cardsanaia->amounts;
                    $itemcomp['avaliable'] = $cardsanaia->avaliable;

                    $itemcomp['purchase'] =   $cardsanaia->purchase;
                    $itemcomp['card_image'] = $cardsanaia->card_image;
                    $itemcomp['nationalcompany'] = $cardsanaia->nationalcompany;
                    $itemcomp['productId'] = $cardsanaia->productId;
                    $itemcomp['enable'] =  $cardsanaia->enable;
                    $itemcomp['api2'] = $cardsanaia->api2;
                    $itemcomp['api2id'] =   $cardsanaia->api2id;
                    cards_anais::create($itemcomp);
                }


                if ($dubiapi->api == 1) {


                    if (isset($balancenational) && !empty($balancenational) && $balancenational != 'error code: 1020') {


                        // $this->InitiateTlync('1',$request->client_number,$request->client_email);


                        ////////////dubai api///////////////

                        $client =  Client::where('id', $order->client_id)->first();
                        $curl = curl_init();
                        $refrenceid = "Merchant_" . rand();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://taxes.like4app.com/online/create_order",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => array(
                                'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                                'email' => 'info@bn-plus.ly',
                                'password' => 'db7d8028631f3351731cf7ca0302651d',
                                'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                                'langId' => '1',
                                'productId' => $order->card_id,
                                'referenceId' => $refrenceid,
                                'time' => time(),
                                'hash' => $this->generateHash($clientdata->phone, $clientdata->email),

                            ),

                        ));

                        $createorder = curl_exec($curl);

                        $json = json_decode($createorder, true);
                        foreach ($json['serials'] as $row) {
                            $updatecardprice['card_code'] =  $this->decryptSerial($row['serialCode']);
                            Cards::where('id', $order->card_id)->update($updatecardprice);
                            $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                            /* $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                            $itemcomp->id = $cardsanaia->id;
                            $itemcomp->order_id =  $order->id;
                            $itemcomp->card_name = $cardsanaia->card_name;
                            $itemcomp->company_id = $cardsanaia->company_id;
                            $itemcomp->api =  $cardsanaia->api;
                            $itemcomp->card_price =  $cardsanaia->card_price;
                            $itemcomp->card_code = $this->decryptSerial($row['serialCode']);
                            $itemcomp->amounts = $cardsanaia->amounts;
                            $itemcomp->avaliable = $cardsanaia->avaliable;
                            $itemcomp->purchase =  $cardsanaia->purchase;
                            $itemcomp->card_image = $cardsanaia->card_image;
                            $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                            $itemcomp->productId = $cardsanaia->productId;
                            $itemcomp->enable = $cardsanaia->enable;
                            $itemcomp->api2 = $cardsanaia->api2;
                            $itemcomp->api2id = $cardsanaia->api2id;


                            $itemcomp->save();*/


                            $itemcomp['id'] =  $cardsanaia->id;
                            $itemcomp['order_id'] =  $order->id;
                            $itemcomp['card_name'] = $cardsanaia->card_name;
                            $itemcomp['company_id'] = $cardsanaia->company_id;
                            $itemcomp['api'] = $cardsanaia->api;
                            $itemcomp['card_price'] = $cardsanaia->card_price;
                            $itemcomp['card_code'] = $this->decryptSerial($row['serialCode']);
                            $itemcomp['amounts'] = $cardsanaia->amounts;
                            $itemcomp['avaliable'] = $cardsanaia->avaliable;

                            $itemcomp['purchase'] =   $cardsanaia->purchase;
                            $itemcomp['card_image'] = $cardsanaia->card_image;
                            $itemcomp['nationalcompany'] = $cardsanaia->nationalcompany;
                            $itemcomp['productId'] = $cardsanaia->productId;
                            $itemcomp['enable'] =  $cardsanaia->enable;
                            $itemcomp['api2'] = $cardsanaia->api2;
                            $itemcomp['api2id'] =   $cardsanaia->api2id;
                            cards_anais::create($itemcomp);
                        }


                        curl_close($curl);
                    } else {


                        return response()->json(['status' => 'error']);
                    }
                }

                if ($dubiapi->api2 == 1) {

                    // $this->InitiateTlync('1',$request->client_number,$request->client_email);


                    $client =  Client::where('id', $order->client_id)->first();
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

                    $orders = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => $alltoken,

                    ])->post(
                        'https://gateway.anis.ly/api/consumers/v1/order',
                        [
                            'walletId' => 'E1521F1F-C592-42F3-7A1A-08D9F31F6661',
                            'cardId' => $dubiapi->api2id,
                            'pinNumber' => '1988',
                            'orderId' => rand(),
                            'quantity' => 1,
                            'TotalValue' => $dubiapi->old_price,
                        ]

                    );

                    if (isset($orders->json()['data'])) {
                        foreach ($orders->json()['data']['cards'] as $cardddds) {
                            $updatecardprssice['card_code'] = $cardddds['secretNumber'];
                            Cards::where('id', $order->card_id)->update($updatecardprssice);
                            $Anaiscodes['client_id'] = $order->client_id;
                            $Anaiscodes['card_code'] = $cardddds['secretNumber'];
                            $Anaiscodes['order_id'] = $order->id;
                            Anaiscodes::create($Anaiscodes);
                            $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                            /* $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                            $itemcomp->id = $cardsanaia->id;
                            $itemcomp->order_id =  $order->id;
                            $itemcomp->card_name = $cardsanaia->card_name;
                            $itemcomp->company_id = $cardsanaia->company_id;
                            $itemcomp->api =  $cardsanaia->api;
                            $itemcomp->card_price =  $cardsanaia->card_price;
                            $itemcomp->card_code = $cardddds['secretNumber'];
                            $itemcomp->amounts = $cardsanaia->amounts;
                            $itemcomp->avaliable = $cardsanaia->avaliable;
                            $itemcomp->purchase =  $cardsanaia->purchase;
                            $itemcomp->card_image = $cardsanaia->card_image;
                            $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                            $itemcomp->productId = $cardsanaia->productId;
                            $itemcomp->enable = $cardsanaia->enable;
                            $itemcomp->api2 = $cardsanaia->api2;
                            $itemcomp->api2id = $cardsanaia->api2id;
                            $itemcomp->save();*/


                            $itemcomp['id'] =  $cardsanaia->id;
                            $itemcomp['order_id'] =  $order->id;
                            $itemcomp['card_name'] = $cardsanaia->card_name;
                            $itemcomp['company_id'] = $cardsanaia->company_id;
                            $itemcomp['api'] = $cardsanaia->api;
                            $itemcomp['card_price'] = $cardsanaia->card_price;
                            $itemcomp['card_code'] = $cardddds['secretNumber'];
                            $itemcomp['amounts'] = $cardsanaia->amounts;
                            $itemcomp['avaliable'] = $cardsanaia->avaliable;

                            $itemcomp['purchase'] =   $cardsanaia->purchase;
                            $itemcomp['card_image'] = $cardsanaia->card_image;
                            $itemcomp['nationalcompany'] = $cardsanaia->nationalcompany;
                            $itemcomp['productId'] = $cardsanaia->productId;
                            $itemcomp['enable'] =  $cardsanaia->enable;
                            $itemcomp['api2'] = $cardsanaia->api2;
                            $itemcomp['api2id'] =   $cardsanaia->api2id;
                            cards_anais::create($itemcomp);
                        }
                    }
                    $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $dubiapi->api2id . '';

                    $cardschek = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => $alltoken,

                    ])->get($compurlcheck);
                    if (isset($cardschek->json()['data'])) {
                        if ($cardschek->json()['data']['inStock'] == false) {
                            $updatecard['purchase'] = 1;
                            $updatecard['avaliable'] = 1;
                            Cards::where('id', $order->card_id)->update($updatecard);
                        }
                    }
                }
                /////////////
                if ($order->update()) {
                    if ($dubiapi->api2 == 0) {
                        $updatecard['purchase'] = 1;
                        $updatecard['avaliable'] = 1;
                        Cards::where('id', $order->card_id)->update($updatecard);
                    }
                    $cardemail =  Cards::where('id', $order->card_id)->first();
                    $client =  Client::where('id', $order->client_id)->first();
                    if ($dubiapi->api == 0) {
                        //$this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');
                    }
                    return response()->json(['status' => 'success']);
                } else {
                    return response()->json(['status' => 'error']);
                }
            }
        } else {
            return response()->json(['status' => 'error']);
        }
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

    function generateHash($phone, $mail)
    {
        $email = strtolower($mail);
        $key = hash('sha256', 't-3zafRa');
        $time = time();
        return hash('sha256', $time . $email . $phone . $key);
    }


    function decryptSerial($encrypted_txt)
    {
        $secret_key = 't-3zafRa';
        $secret_iv = 'St@cE4eZ';
        $encrypt_method = 'AES-256-CBC';
        $key = hash('sha256', $secret_key);

        //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);
    }


    /////////////////Tlyncapi/////////////////


    public function InitiatePaymenttly(Request $request, $order_id)
    {

        // dd( 's');
        $custom_ref = rand();

        $amount = $request->card_price;
        $phones = $request->client_number;
        $email = $request->client_email;


        if ($amount < 5) {

            $message = " القيمة أقل من الحد الأدنى  5 دينار ";
            return $this->apiResponse6(null, null, $message, 404);
        }


        $fronturl = url('api/fronturl');

        $backendurl = url('api/backendurl');


        if (substr($phones, 0, 6) == '+218 9') {

            $phone =  str_replace('+218 ', '', $phones);
        } else  if (substr($phones, 0, 6) == '+218 0') {
            $phone =  str_replace('+218 0', '', $phones);
        } else  if (substr($phones, 0, 5) == '+2189') {
            $phone =  str_replace('+218', '', $phones);
        } else  if (substr($phones, 0, 5) == '+2180') {
            $phone =  str_replace('+2180', '', $phones);
        } else  if (substr($phones, 0, 1) == '0') {
            $phone =  str_replace('0', '', $phones);
        } else {
            $phone =  $phones;
        }
        // return $phones;
        //return substr($phones, 0, 1);

        if (
            preg_match("~^2189\d+$~", $phone) || preg_match("~^9\d+$~", $phone) || preg_match("~^002189\d+$~", $phone)
            || preg_match("~^0021809\d+$~", $phone)   || preg_match("~^21809\d+$~", $phone)
        ) {




            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wla3xiw497.execute-api.eu-central-1.amazonaws.com/payment/initiate',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'id=pLAkv8XEQemV7wDM6ElodG24RnzXAKZVE49PjrN5Y8OgLBJ01xyqabvkpbo5Vq02&amount=' . $amount . '&phone=' . $phone . '&email=' . $email . '&backend_url=' . $backendurl . '
 &custom_ref=' . $custom_ref .
                    '&frontend_url=' . $fronturl . '',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                    'Authorization: Bearer 1eoT4IqACu7X0iZvvUeoCbfF0PFlBSuNHVdF6Dzn'
                ),
            ));

            //  1eoT4IqACu7X0iZvvUeoCbfF0PFlBSuNHVdF6Dzn
            // 'Authorization: Bearer 0r14CIjDO0MDErCakVQsoohAvvyklGUvbxUAXpXg'
            //'id=MBQKYkEgg27WdwDV6O49QnERrLJNqY8AYx8a5KMyXej1GzoAb3kBl0xmPz23mV0o
            //https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/payment/initiate
            $response = curl_exec($curl);


            $json = json_decode($response, true);


            $request_data['result'] = $json['result'];
            $request_data['custom_ref'] =  $json['custom_ref'];
            $request_data['url'] = $json['url'];
            $request_data['card_id'] = $request->card_id;
            $request_data['order_id'] = $order_id;
            $request_data['amount'] = 1;
            $request_data['client_name'] = $request->client_name;
            $request_data['client_id'] = $request->client_id;
            $request_data['customer_phone'] = $request->client_number;
            $request_data['client_email'] = $request->client_email;
            $request_data['transaction_id'] = rand();




            $order = tdawalDetails::create($request_data);
            curl_close($curl);
            //$webview="https://bn-plus.ly/BNplus/public/api/reservewebview/".$order->id;
            $webview = $json['url'];

            $message = "card reserved ";

            return $this->apiResponseweb($webview, $message, 200);
        } else {
            return $this->apiResponse6(null, null, 'Enter Valid number', 404);
        }
    }



    public function transactionPayment(Request $request)
    {
        //  id MBQKYkEgg27WdwDV6O49QnERrLJNqY8AYx8a5KMyXej1GzoAb3kBl0xmPz23mV0o
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/receipt/transaction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'store_id=' . $request->store_id . '&transaction_ref=' . $request->transaction_ref . '&custom_ref=' . $request->custom_ref . '',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'Authorization: Bearer 0r14CIjDO0MDErCakVQsoohAvvyklGUvbxUAXpXg'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }


    public function reservewebview($id)
    {


        return view('webview', compact('id'));
    }

    public function backendfun(Request $request)
    {

        $json = $request->all();

        $request_data['result'] = $json['result'];
        $request_data['amount'] = $json['amount'];
        $request_data['store_id'] = $json['store_id'];
        $request_data['our_ref'] = $json['our_ref'];
        $request_data['payment_method'] = $json['payment_method_en'];
        $request_data['customer_phone'] = $json['customer_phone'];
        $request_data['custom_ref'] = $json['custom_ref'];
        $request_data['charges'] = $json['charges'];
        $request_data['net_amount'] = $json['net_amount'];
        if ($json['result'] == 'success') {
            $request_data['paid'] = 'yes';
        }

        $order = tdawalDetails::where('custom_ref', $json['custom_ref'])->update($request_data);

        $orderdone = tdawalDetails::where(array('custom_ref' => $json['custom_ref'], 'paid' => 'yes'))->first();

        $this->confirmorders($orderdone->client_id, $orderdone->order_id, $orderdone->client_email, $orderdone->client_name, $orderdone->customer_phone, $orderdone->transaction_id);
    }
}
