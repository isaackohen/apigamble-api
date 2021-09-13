<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Games;
use App\Http\Controllers\PaymentTatumController;

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

Route::group(['middleware' => ['IPCheck']], function () {
Route::any('slots/callbackurl', 'SessionController@getCallbackLink');
});


Route::any('slots/createSession/{apikey}/{playerId}/{gameId}/{mode}', 'SessionController@createSession');
Route::any('slots/createBonusSession/{apikey}/{playerId}/{gameId}/{mode}/{fscount}', 'SessionController@createBonusSession');
Route::any('slots/createDemoSession/{apikey}/{gameId}', 'SessionController@createDemoSession');
Route::any('slots/listGames', 'CallbackController@listGames');

Route::middleware('throttle:30,1')->get('/slots/listGames',function() {
$Games = DB::table('Games')->get();
    return $Games; });

Route::any('sports/prematch/{apikey}/{sport_id}', 'SportsController@getPrematchOdds');
Route::any('sports/inplay/{apikey}/{sport_id}', 'SportsController@getInplay');
Route::any('sports/result/{apikey}/{event_id}', 'SportsController@getGameResult');
Route::any('sports/event/{apikey}/{event_id}', 'SportsController@getGameEvent');
Route::any('payment/createShiba/{apikey}/{currency}/{token}/{label}', 'PaymentController@createTokenAddress');

Route::any('paydash', 'CallbackPaydash@callbackTester');



Route::any('payment/creditcard/{apikey}/{email}/{amount}/{label}', 'PaydashController@create');

Route::any('payment/createAddress/{apikey}/{currency}/{label}', 'PaymentTatumController@createAddress');
Route::any('payment/sendCrypto/{apikey}/{currency}/{amount}/{from}/{to}/{masterpass}', 'PaymentTatumController@sendCrypto');

Route::any('payment/generateTatumWallets/{apikey}', 'PaymentSetupController@setupBtc');



Route::middleware('throttle:30,1')->get('/slots/listGames/{provider}',function($provider) {
$Games = DB::table('Games')->where('gameprovider', '=', $provider)->get();
    return $Games; });

//Route::any('payment/createAddress/{apikey}/{currency}/{label}', 'PaymentController@createWalletAddress');
//Route::any('payment/createTokenAddress/{apikey}/{currency}/{token}/{label}', 'PaymentController@createTokenAddress');

//Route::any('payment/sendToken/{apikey}/{currency}/{amount}/{from}/{to}/{masterpass}', 'PaymentController@sendToken');

//Route::any('payment/getBalanceToken/bsc/{currency}/{address}', 'PaymentController@getBalanceTokenbsc');
//Route::any('payment/getBalance/bsc/{address}', 'PaymentController@getBalancebsc');
//Route::any('payment/getBalance/trx/{address}', 'PaymentController@getBalancetrx');
//Route::any('payment/getBalance/eth/{address}', 'PaymentController@getBalanceeth');


Route::any('callback/tatumWebhook', 'CallbackTatumController@tatumWebhook');

Route::any('callback/tatumWebhookPending', 'CallbackTatumController@pending');
Route::any('callback/tatumWebhookWithdrawal', 'CallbackTatumController@withdrawal');


Route::any('callback/paymentDeposits', 'CallbackController@paymentDeposits');

Route::any('callback/callbackTester', 'CallbackController@callbackTester');
/*


Route::any('walletNotify/{currency}/{txid}', 'PaymentController@process');

Route::any('bitgoRunning', function() {
            $json = json_decode(file_get_contents('http://localhost:3080/api/v2/ping'), true);
            return $json['status'] === 'service is ok!';
}); 


Route::any('bitgoWebhookltc', 'PaymentController@bitgoWebhookltc');

Route::any('sendLTC/{from}/{to}/{sum}', 'PaymentController@sendLTC');

Route::any('bitgoWebhook', function() {
    $sdk = Currency::find('bg_btc')->getSDK();
    $payload = $sdk->getWebhookPayload();

    $currency = Currency::find('bg_'.$payload['coin']);
    if($currency === null) {
        Log::error('Invalid BitGo webhook currency: bg_'.$payload['coin']);
        return reject(1, 'Invalid request');
    }

    $result = $currency->process();
    return success(is_array($result) ? $result : []);
});
*/

