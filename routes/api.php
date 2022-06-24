<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/////////
Route::middleware('jwtAuth')->group(function() {
    Route::get('logout','AuthController@logout');
    Route::get('me','AuthController@me');
    Route::get('payload','AuthController@payload');
    Route::resource('posts','postController');

/////////////////////////////
Route::resource('companies','CompanyController');
Route::resource('cards','CardController');
Route::post('localcards','CardController@localcards');
Route::post('nationalcards','CardController@nationalcards');
//////////used apies
Route::post('allcompanies','CompanyController@allcompanies');
Route::post('cardsbycompany','CardController@cardsbycompany');
Route::post('cardscount','CardController@cardscount');

Route::post('clientorder','OrderController@clientorder');

//////////////////////Sadad API 
Route::post('verify','SadadController@verify');
Route::post('confirm','SadadController@confirm');
//////////////////////////

////////////////////////tadalwal////////////////////
//Route::post('reservetadalwalorder','TadawalController@reservetadalwalorder');
Route::post('InitiateTlync','TadawalController@reservetadalwalorder');


Route::post('confirmorders','TadawalController@confirmorders');
//Route::post('InitiateTlync','TadawalController@InitiatePaymenttly');
Route::post('receipt/transaction','OrderController@transactionPayment');
////////////////////////////معاملات//////////////////////
Route::post('reserveorder','OrderController@reserveorder');
Route::post('finalorder','OrderController@finalorder');
///////////////////////////

});


Route::post('check_balance','CompanyController@check_balance');

//Route::post('clientordser','OrderController@clientorder');

/////////////////////web view 
            Route::get('reservewebview/{id}', 'TadawalController@reservewebview')->name('reservewebview');
            Route::get('/success', function () {
                return 'success';
});

          Route::get('/fail', function () {
                return 'Fail';
});

         Route::get('/fronturl', function () {
              //  return 'success';
                 return View::make("tdawalsuccess");

              
});

         Route::post('backendurl', 'TadawalController@backendfun')->name('backendfun');

            
            /////////////////

///



Route::post('login','AuthController@login');
//Route::middleware('jwt.auth')->post('login', 'API/AuthController@login');
