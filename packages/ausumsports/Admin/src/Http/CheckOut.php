<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Events\Logger as EventLogger;
use Ausumsports\Admin\Models\Admin;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Braintree\Customer;
use Braintree\ClientToken;
use Braintree\Transaction;
use Braintree\TransactionSearch;
use Braintree\Gateway;
use Illuminate\Support\Facades\Auth;

class CheckOut extends Controller
{

    /**
     * @var Braintree\Gateway
     */
    private $gateway;

    function __construct()
    {

        $this->gateway = new Gateway([
            'environment' => env('BTREE_ENVIRONMENT'),
            'merchantId' => env('BTREE_MERCHANT_ID'),
            'publicKey' => env('BTREE_PUBLIC_KEY'),
            'privateKey' => env('BTREE_PRIVATE_KEY')
        ]);


    }

    function generateToken()
    {
        return ClientToken::generate();
    }

    function setCustomerId()
    {
        $customer = Auth::user();

        // using your customer id we will create
        // brain tree customer id with same id
        $response = Customer::create([
            'id' => $customer->id
        ]);

        // save your braintree customer id
        if ($response->success) {
            $customer->braintree_customer_id = $response->customer->id;
            $customer->save();
        }

    }

    public function index()
    {
        return View("adminViews::checkout/index", ['sub' => 'checkout']);
    }

    public function list()
    {

        $customer = Auth::user();

        $collection = $this->gateway->transaction()->search(/*[
            TransactionSearch::customerId()->is($customer->id),
        ]*/);


        return View("adminViews::checkout/list", ['list' => $collection,'sub' => 'checkout']);
    }

    public function process(Request $request)
    {
        $payload = $request->input('payload', false);
        $nonce = $payload['nonce'];

        $status = Transaction::sale([
            'amount' => '10.00',
            'paymentMethodNonce' => $nonce,
            'customer' => [
                'firstName' => 'Drew',
                'lastName' => 'Smith',
                'company' => 'Braintree',
                'phone' => '312-555-1234',
                'fax' => '312-555-1235',
                'website' => 'http://www.example.com',
                'email' => 'drew@example.com'
            ],
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        Log::info($status);

        return response()->json($status);

        /*
         * $result = $gateway->transaction()->sale([
  'amount' => '100.00',
  'orderId' => 'order id',
  'merchantAccountId' => 'a_merchant_account_id',
  'paymentMethodNonce' => $nonceFromTheClient,
  'deviceData' => $deviceDataFromTheClient,
  'customer' => [
    'firstName' => 'Drew',
    'lastName' => 'Smith',
    'company' => 'Braintree',
    'phone' => '312-555-1234',
    'fax' => '312-555-1235',
    'website' => 'http://www.example.com',
    'email' => 'drew@example.com'
  ],
  'billing' => [
    'firstName' => 'Paul',
    'lastName' => 'Smith',
    'company' => 'Braintree',
    'streetAddress' => '1 E Main St',
    'extendedAddress' => 'Suite 403',
    'locality' => 'Chicago',
    'region' => 'IL',
    'postalCode' => '60622',
    'countryCodeAlpha2' => 'US'
  ],
  'shipping' => [
    'firstName' => 'Jen',
    'lastName' => 'Smith',
    'company' => 'Braintree',
    'streetAddress' => '1 E 1st St',
    'extendedAddress' => 'Suite 403',
    'locality' => 'Bartlett',
    'region' => 'IL',
    'postalCode' => '60103',
    'countryCodeAlpha2' => 'US'
  ],
  'options' => [
    'submitForSettlement' => true
  ]
]);
         */

    }


    function view()
    {
        $customer = Auth::user();

            $braintree_customer_id = $customer->braintree_customer_id ?? $this->setCustomerId();

            $token = $this->generateToken();

        return View("adminViews::checkout/checkout", ['braintree_customer_id' => $braintree_customer_id, 'token'=>$token, 'sub' => 'checkout']);
    }


}
