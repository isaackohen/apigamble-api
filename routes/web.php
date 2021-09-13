<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelayController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CallbackPaydash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| done: 0x80aa35630b1611e50c37dcb1cc73dd5e7ebc0290
*/
Route::get('/', function () {
    return view('welcome');
});

Route::any('/callback/withdrawAndDeposit/{operator}/{user}/{withdraw}/{deposit}/{currency}/{gameid}/{txid}', 'RelayController@withdrawAndDeposit');


 Route::any('/creditcard/success/',function() {

    return view('paymentsuccess'); });



 Route::any('/creditcard/checkout/{order}/',function($order) {
    $redirectURL = $order;

    return view('checkout', ['url' => $redirectURL]); });


/*
|--------------------------------------------------------------------------
| Notes
|--------------------------------------------------------------------------
|
| Old webroutes

Route::get('/ddddqwwqwqqwddddwqqw', function () {

$apikey = "6c2d25060e262c489df25422de1840bd12e547c9"; // API Key in your account panel
$contractaddress = "0x9a946c3cb16c08334b69ae249690c236ebd5583e"; // Smart contract address of the Token
$from = "0x68f3f2cfbc091a35abace9c7a606fe35e0cef8e2"; // Binancecoin address you want to send from (must have been created with Chaingateway.io)
$to = "0x26d8445b6a20d10C077A163e88BACceF97Fe2f04"; // Receiving Binancecoin address
$password = "czaPvWGYKP9DH6Er"; // Password of the Binancecoin address (which you specified when you created the address)
$amount = "28"; // Amount of Tokens to send

# Define function endpoint
$ch = curl_init("https://eu.bsc.chaingateway.io/v1/sendToken");

# Setup request to send json via POST. This is where all parameters should be entered.
$payload = json_encode( array("contractaddress" => $contractaddress, "from" => $from, "to" => $to, "password" => $password, "amount" => $amount) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: " . $apikey));

# Return response instead of printing.
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

# Send request.
$result = curl_exec($ch);
curl_close($ch);

# Decode the received JSON string
$resultdecoded = json_decode($result, true);

# Print the transaction id of the transaction
echo $result;

});

Route::get('/dee22ee', function () {
    
        $payload = json_encode(array(
                  "password" => "czaPvWGYKP9DH6Er",
                  "gas" => "6",
                  "apikey" => "6c2d25060e262c489df25422de1840bd12e547c9",
                  "newaddress" => "0x26d8445b6a20d10C077A163e88BACceF97Fe2f04",
                  "binancecoinaddress" => "0x68f3f2cfbc091a35abace9c7a606fe35e0cef8e2") );
                  
        $churl = 'https://eu.bsc.chaingateway.io/v1/clearAddress';


        $chpost = curl_init($churl);

        # Setup request to send json via POST. This is where all parameters should be entered.
        curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 6c2d25060e262c489df25422de1840bd12e547c9"));

        # Return response instead of printing.
        curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

        # Send request.
        $result = curl_exec($chpost);
        curl_close($chpost);

        # Decode the received JSON string
        $resultdecoded = json_encode($result, true);
        # Print status of request (should be true if it worked)

    return $resultdecoded;
});




Route::get('/getbally/', function () {

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/subscription/report/60fcd3f7d832fee9ee3137f3",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);


$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});


Route::get('/getkey/', function () {



curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/bitcoin/wallet/priv",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"index\":67,\"mnemonic\":\"atoms_purge_irate_generals_and_magazines_bounce_liberal_eyedroppers\"}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});


Route::get('/pending/', function () {


$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/bitcoin/transaction",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"fromAddress\":[{\"address\":\"1NctEXdN2oi5pdeaDxDFcQDhFNiTmfpyG8\",\"privateKey\":\"L5fmW8STQvX7vGwhFpK9awaS3UmyDAhGRyD7AbfbuLzc6Narx4qo\"}],\"to\":[{\"address\":\"bc1qct9vn5yrutc7yvjl0vspn5knxpj2m0lv0zdvep\",\"value\":0.0001}]}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});


Route::get('/dogecoin/', function () {


$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/dogecoin/wallet?mnemonic=atoms_purge_irate_generals_and_magazines_bounce_liberal_eyedroppers",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});


Route::get('/ledgeraccount/', function () {


$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/ledger/account",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"currency\":\"DOGE\",\"xpub\":\"xpub6EBdASC6Krcr8earstwU71phNp3gEA2CH9DT61UMLK8trbEWH4oZa5UZhfv2rzJgPpqKopQUUeQvVX6o1fnpZaY3gHzaEuYbhve9sm5v1fJ\",\"customer\":{\"accountingCurrency\":\"USD\",\"customerCountry\":\"US\",\"externalId\":\"123654\",\"providerCountry\":\"US\"},\"compliant\":false,\"accountCode\":\"AC_1011_B\",\"accountingCurrency\":\"USD\",\"accountNumber\":\"70CCD41D29AAA4DA2B7AB6932C0690CA0BCH\"}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else { 
  echo $response;
}


});


Route::get('/tatumWebhook/', function () {

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/subscription",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"type\":\"ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION\",\"currency\":\"BSC\",\"attr\":{\"id\":\"60ff4e98c857d70c624efc30\",\"url\":\"https://apigamble.com/api/callback/tatumWebhook\"}}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});




Route::get('/newaccbsc/', function () {

//0xdbaea9424ce13fcc8f6a5fd6db60a00837f24394

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/account/60fd4fb98b3e93a23ff06afe/address?index=2",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});



Route::get('/setbetshiba/', function () {

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/token/BETSHIBA/0x24aff9387eabdec994d7a7049e0c1b2bd4120eeb",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

});


Route::get('/betshiba/', function () {


$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/ledger/account",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"currency\":\"BETSHIBA\",\"xpub\":\"xpub6FK5DqMWcrrts7Bt7h2PNgQPK4t4h6Mk8Rej9g1Gk8bbe7YEFLaE3So34GgWs8GwTYLt9DYc6m9mLX59Cz2a8Cu2kqyHmNoHnsqJudiYYVw\",\"customer\":{\"accountingCurrency\":\"USD\",\"customerCountry\":\"US\",\"externalId\":\"123654\",\"providerCountry\":\"US\"},\"compliant\":false,\"accountCode\":\"AC_1011_B\",\"accountingCurrency\":\"USD\",\"accountNumber\":\"70CCD41D59AAA4DA2B7AB6932C0690CA0xBLZD-new\"}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else { 
  echo $response;
}


});

Route::get('/tatumlistaccounts/', function () {


$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/ledger/account?pageSize=30&offset=0",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

});

Route::get('/setgame1token/', function () {
/*

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/bsc/bep20",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"symbol\":\"GAME1\",\"supply\":\"1000000.0\",\"decimals\":8,\"description\":\"My Public Token\",\"basePair\":\"EUR\",\"baseRate\":1,\"customer\":{\"accountingCurrency\":\"USD\",\"customerCountry\":\"US\",\"externalId\":\"123654\",\"providerCountry\":\"US\"},\"accountingCurrency\":\"USD\",\"derivationIndex\":1,\"xpub\":\"xpub6EzSmrXFJLgdh7hmiGb3meLefwA7hpaD431f3B3MuEeADHok613PFBbiPTtzLD4hfjCTgPS38jiSJvGDNsDZJ4iByC12ksWz9MdpzsNSoaQ\"}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
} 

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/ledger/account",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"currency\":\"BSC\",\"xpub\":\"xpub6EzSmrXFJLgdh7hmiGb3meLefwA7hpaD431f3B3MuEeADHok613PFBbiPTtzLD4hfjCTgPS38jiSJvGDNsDZJ4iByC12ksWz9MdpzsNSoaQ\",\"customer\":{\"accountingCurrency\":\"USD\",\"customerCountry\":\"US\",\"externalId\":\"123654\",\"providerCountry\":\"US\"},\"compliant\":false,\"accountCode\":\"260E8334DBF0B5503CCFD76347AC49FDGAME1\",\"accountingCurrency\":\"USD\",\"accountNumber\":\"123456\"}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

/*
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/bsc/bep20/GAME1/0x0E52d24c87A5ca4F37E3eE5E16EF5913fb0cCEEB",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});



Route::get('/newgame1wallet/', function () {

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/bsc/wallet?mnemonic=Game1_Mercury_Venus_Earth_Mars_Jupiter_Saturn_Uranus_Neptune_Pluto",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
});


Route::get('/listAddressddes2/', function () {
    
                $payload = json_encode(array(
                  "contractaddress" => "0x9a946c3cb16c08334b69ae249690c236ebd5583e",
                  "binancecoinaddress" => "0xa6aeddb34be616678568832da4060b8de51e5cec") );

        $churl = 'https://eu.bsc.chaingateway.io/v1/getTokenBalance';


        $chpost = curl_init($churl);

        # Setup request to send json via POST. This is where all parameters should be entered.
        curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 6c2d25060e262c489df25422de1840bd12e547c9"));

        # Return response instead of printing.
        curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

        # Send request.
        $result = curl_exec($chpost);
        curl_close($chpost);

        # Decode the received JSON string
        $resultdecoded = json_encode($result, true);
        # Print status of request (should be true if it worked)

    return $resultdecoded;
});

Route::get('/betshibaregister', function () {

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/bsc/bep20",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"symbol\":\"BETSHIBA\",\"supply\":\"1000000.0\",\"decimals\":8,\"description\":\"xBLZDToken\",\"basePair\":\"EUR\",\"baseRate\":1,\"customer\":{\"accountingCurrency\":\"USD\",\"customerCountry\":\"US\",\"externalId\":\"123654\",\"providerCountry\":\"US\"},\"accountingCurrency\":\"USD\",\"derivationIndex\":3,\"xpub\":\"xpub6FK5DqMWcrrts7Bt7h2PNgQPK4t4h6Mk8Rej9g1Gk8bbe7YEFLaE3So34GgWs8GwTYLt9DYc6m9mLX59Cz2a8Cu2kqyHmNoHnsqJudiYYVw\"}",
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

});


Route::get('/exportAddress', function () {
    
        $payload = json_encode(array(
            "apikey"      => "6c2d25060e262c489df25422de1840bd12e547c9",
            "password" => "czaPvWGYKP9DH6Er",

            "binancecoinaddress" => "0xe38270254015b0d688cb3c29b37fad0fc59c9b9b") );

        $churl = 'https://eu.bsc.chaingateway.io/v1/exportAddress';

        $chpost = curl_init($churl);

        # Setup request to send json via POST. This is where all parameters should be entered.
        curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 6c2d25060e262c489df25422de1840bd12e547c9"));

        # Return response instead of printing.
        curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

        # Send request.
        $result = curl_exec($chpost);
        curl_close($chpost);

        # Decode the received JSON string
        $resultdecoded = json_encode($result, true);
        # Print status of request (should be true if it worked)

    return $result;


});

*/


