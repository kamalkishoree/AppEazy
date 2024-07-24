<?php

namespace App\Http\Controllers\Front;

use Auth;
use App\Models\PaymentOption;
use Illuminate\Http\Request;
use App\Models\{Order, User, Cart, ClientCurrency, CartProduct};
use App\Http\Traits\ApiResponser;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Front\{FrontController, CashfreeGatewayController,EasebuzzController,VnpayController, PayUGatewayController, MyCashGatewayController,UseRedePaymentController,OpenpayPaymentController};


class PaymentController extends FrontController{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $user = Auth::user();
        $vendor_min_amount_errors = [];
        $cart = Cart::where('user_id', $user->id)->first();
        if($cart){
            $currency_id = Session::get('customerCurrency');
            $currency_symbol = Session::get('currencySymbol');
            $clientCurrency = ClientCurrency::where('currency_id', $currency_id)->first();
            $cart_products = CartProduct::with('vendor','product.pimage', 'product.variants', 'product.taxCategory.taxRate','coupon', 'product.addon')->where('cart_id', $cart->id)->where('status', [0,1])->where('cart_id', $cart->id)->orderBy('created_at', 'asc')->get();
            $dollar_compare = $clientCurrency ? $clientCurrency->doller_compare : 1;
            foreach ($cart_products->groupBy('vendor_id') as $vendor_id => $vendor_cart_products) {
                $vendor_detail = [];
                $vendor_payable_amount = 0;
                $vendor_discount_amount = 0;
                foreach ($vendor_cart_products as $vendor_cart_product) {
                    $vendor_detail = $vendor_cart_product->vendor;
                    $variant = $vendor_cart_product->product->variants->where('id', $vendor_cart_product->variant_id)->first();
                    $quantity_price = 0;
                    $divider = $vendor_cart_product->doller_compare ? $vendor_cart_product->doller_compare : 1;
                    $price_in_currency = $variant->price / $divider;
                    $price_in_dollar_compare = $price_in_currency * $dollar_compare;
                    $quantity_price = $price_in_dollar_compare * $vendor_cart_product->quantity;
                    $vendor_payable_amount = $vendor_payable_amount + $quantity_price;
                    $product_taxable_amount = 0;
                    $product_payable_amount = 0;
                    if(!empty($vendor_cart_product->addon)){
                        foreach ($vendor_cart_product->addon as $ck => $addon) {
                            $opt_quantity_price = 0;
                            $opt_price_in_currency = $addon->option->price??0;
                            $opt_price_in_doller_compare = $opt_price_in_currency * $dollar_compare;
                            $opt_quantity_price = $opt_price_in_doller_compare * $vendor_cart_product->quantity;
                            $vendor_payable_amount = $vendor_payable_amount + $opt_quantity_price;
                        }
                    }
                }
                // if($vendor_detail){
                //     if($vendor_detail->order_min_amount > 0){
                //         if($vendor_payable_amount < $vendor_detail->order_min_amount){
                //             $vendor_min_amount_errors[]= array(
                //                 'vendor_id' => $vendor_detail->id,
                //                 'message' => "Minimum order should be more than  $currency_symbol $vendor_detail->order_min_amount",
                //             );
                //         }
                //     }
                // }
            }
            // if(count($vendor_min_amount_errors) > 0){
            //     return $this->errorResponse($vendor_min_amount_errors, 402);
            // }
        }
        $checkCod = '';
        $codMinAmount = PaymentOption::select('credentials')->where('code','cod')->value('credentials');
        $cod = json_decode($codMinAmount);
        if(isset($cod->cod_min_amount) && ($cod->cod_min_amount>0 && $cart->payable_amount < $cod->cod_min_amount))
        {
            $checkCod = 'cod';
        }
        $ex_codes = ['cod'];
        //mohit sir branch code added by sohail
        $serviceType =  Session::get('vendorType');
        $getAdditionalPreference = getAdditionalPreference(['advance_booking_amount', 'advance_booking_amount_percentage','is_cod_payment','is_prepaid_payment']);
        if($serviceType == 'takeaway' && !empty($getAdditionalPreference['advance_booking_amount']) && !empty($getAdditionalPreference['advance_booking_amount_percentage']) && ($getAdditionalPreference['advance_booking_amount_percentage'] > 0) && ($getAdditionalPreference['advance_booking_amount_percentage'] < 101) ){
            
            $payment_options = PaymentOption::select('id', 'code', 'title', 'credentials')->where('status', 1)->where('id', '!=', 1)->get();
        }else{
           
            $payment_options = PaymentOption::select('id', 'code', 'title', 'credentials')->where('status', 1)->get();
        }
                 
        if(@$getAdditionalPreference['is_cod_payment']==1 && @$getAdditionalPreference['is_prepaid_payment']==1){
            $payment_options = PaymentOption::select('id', 'code', 'title', 'credentials')->where('status', 1)->get();
        }elseif(@$getAdditionalPreference['is_prepaid_payment']==1){
            $payment_options = PaymentOption::select('id', 'code', 'title', 'credentials')->where('status', 1)->where('id', '!=', 1)->get();
        }elseif(@$getAdditionalPreference['is_cod_payment']==1){
            $payment_options = PaymentOption::select('id', 'code', 'title', 'credentials')->where('status', 1)->where('id', '=', 1)->get();
        }
        //till here
        foreach ($payment_options as $k => $payment_option) {
            if(((in_array($payment_option->code, $ex_codes)) || (!empty($payment_option->credentials)))){
                $payment_option->slug = strtolower(str_replace(' ', '_', $payment_option->title));
                if($payment_option->code == 'cod'){
                    $payment_option->title = 'Cash  <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 71.26" width="35" height="26"><defs>
    <style>
      .cls-1 { fill: #427d2a; }
      .cls-1, .cls-2 { fill-rule:evenodd; }
      .cls-2 { fill: #87cc71; }
      .cls-3 { fill: #fff; }
    </style>
  </defs>
  <title>cash</title>
  <path class="cls-1" d="M13.37,0H122.88V60.77l-7.74,0,.54-44.59,0-.53a7.14,7.14,0,0,0-7.13-7.14l-95.2,0V0ZM0,14.42H109.51V71.26H0V14.42Z"/>
  <path class="cls-2" d="M91.72,23.25a8.28,8.28,0,0,0,8,8.11V53.85a8.51,8.51,0,0,0-8.5,8.71H18.42a8.38,8.38,0,0,0-8.06-8.5V31.57a8.43,8.43,0,0,0,8.52-8.32Z"/>
  <path class="cls-1" d="M40.28,35.18a16.76,16.76,0,1,1,6.91,22.67,16.75,16.75,0,0,1-6.91-22.67Z"/>
  <path class="cls-3" d="M55.22,55.38a1.54,1.54,0,0,1-1.56-1.56V52.3A11.09,11.09,0,0,1,51,51.82,13.32,13.32,0,0,1,49.06,51a1.61,1.61,0,0,1-.9-1,2,2,0,0,1,.06-1.27,1.66,1.66,0,0,1,.83-.88,1.57,1.57,0,0,1,1.38.12,10.45,10.45,0,0,0,1.73.69,9.31,9.31,0,0,0,2.72.35,4.27,4.27,0,0,0,2.53-.57A1.73,1.73,0,0,0,58.16,47a1.49,1.49,0,0,0-.52-1.16,4.11,4.11,0,0,0-1.86-.75l-2.84-.62q-4.71-1-4.71-5a5.07,5.07,0,0,1,1.48-3.68,6.84,6.84,0,0,1,3.95-1.88V32.29a1.53,1.53,0,0,1,.44-1.1,1.52,1.52,0,0,1,1.12-.45,1.44,1.44,0,0,1,1.08.45,1.53,1.53,0,0,1,.44,1.1v1.55a10.83,10.83,0,0,1,2,.46,7.18,7.18,0,0,1,1.8.88,1.62,1.62,0,0,1,.76,1,1.75,1.75,0,0,1-.12,1.15,1.43,1.43,0,0,1-.84.76,1.76,1.76,0,0,1-1.41-.19,8.26,8.26,0,0,0-1.52-.57,7.57,7.57,0,0,0-2-.23,3.88,3.88,0,0,0-2.35.62,1.89,1.89,0,0,0-.85,1.6,1.48,1.48,0,0,0,.5,1.15,4.11,4.11,0,0,0,1.77.74l2.87.62a7,7,0,0,1,3.64,1.77,4.36,4.36,0,0,1,1.17,3.14,4.79,4.79,0,0,1-1.48,3.62,7.28,7.28,0,0,1-3.87,1.84v1.62a1.52,1.52,0,0,1-.44,1.1,1.45,1.45,0,0,1-1.08.46Z"/>
</svg>
';
                }
                if($payment_option->code == 'stripe'){
                    $payment_option->title = 'Credit/Debit Card (Stripe)';
                }elseif($payment_option->code == 'kongapay'){
                    $payment_option->title = 'Pay Now';
                }elseif($payment_option->code == 'mvodafone'){
                    $payment_option->title = 'Vodafone M-PAiSA';
                }elseif($payment_option->code == 'mobbex'){
                    $payment_option->title = __('Mobbex');
                }
                elseif($payment_option->code == 'offline_manual'){
                    $json = json_decode($payment_option->credentials);
                    $payment_option->title = $json->manule_payment_title;
                }elseif($payment_option->code == 'mycash'){
                    $payment_option->title = __('Digicel MyCash');
                }elseif($payment_option->code == 'windcave'){
                    $payment_option->title = __('Windcave (Debit/Credit card)');
                }elseif($payment_option->code == 'stripe_ideal'){
                    $payment_option->title = __('iDEAL');
                }elseif($payment_option->code == 'authorize_net'){
                    $payment_option->title = __('Credit/Debit Card');
                }elseif($payment_option->code == 'obo'){
                    $payment_option->title = __("MoMo, Airtel Money, Credit/Debit Cards by O'Pay");
                }elseif($payment_option->code == 'livee'){
                    $payment_option->title = __("Livees");
                }
                $payment_option->title = __($payment_option->title);
                unset($payment_option->credentials);
            }
            else{
                unset($payment_options[$k]);
            }
        }
  
        return $this->successResponse($payment_options);
    }

    public function paypalCompleteCheckout(Request $request, $domain = '', $token = '', $action = '', $address_id ='')
    {
        return view('frontend.account.complete-checkout')->with(['auth_token' => $token, 'action' => $action, 'address_id' => $address_id]);
    }

    public function paylinkCompleteCheckout(Request $request, $domain = '', $token = '', $action = '', $address_id ='')
    {
        return view('frontend.account.complete-checkout')->with(['auth_token' => $token, 'action' => $action, 'address_id' => $address_id]);
    }

    public function getCheckoutSuccess(Request $request, $domain = '', $id = '')
    {
        return view('frontend.account.checkout-success');
    }

    public function getGatewayReturnResponse(Request $request)
    {
        return view('frontend.account.gatewayReturnResponse');
    }

    public function verifyPaymentOtp(Request $request, $domain='', $gateway)
    {
        if($gateway == 'mycash'){
            $data = $request->all();
            return view('frontend.payment_gatway.mycash_otp_verify', compact('data'));
        }
    }

    public function verifyPaymentOtpApp(Request $request, $domain='', $gateway)
    {
        if($gateway == 'mycash'){
            $data = $request->all();
            return view('frontend.payment_gatway.mycash_otp_verify', compact('data'));
        }
    }

    public function sendPaymentOtp(Request $request, $domain='', $gateway)
    {
        if(!empty($gateway)){
            $function = 'sendPaymentOtpVia_'.$gateway;
            if(method_exists($this, $function)) {
                if(!empty($request->payment_form)){
                    $response = $this->$function($request); // call related gateway for payment processing
                    return $response;
                }
            }
            else{
                return $this->errorResponse("Invalid Gateway Request", 400);
            }
        }else{
            return $this->errorResponse("Invalid Gateway Request", 400);
        }
    }

    public function sendPaymentOtpVia_mycash(Request $request){
        $gateway = new MyCashGatewayController();
        return $gateway->sendOtp($request);
    }

    public function verifyPaymentOtpSubmit(Request $request, $domain='', $gateway)
    {
        if(!empty($gateway)){
            $function = 'verifyPaymentOtpVia_'.$gateway;
            if(method_exists($this, $function)) {
                if(!empty($request->payment_form)){
                    $response = $this->$function($request); // call related gateway for payment processing
                    return $response;
                }
            }
            else{
                return $this->errorResponse("Invalid Gateway Request", 400);
            }
        }else{
            return $this->errorResponse("Invalid Gateway Request", 400);
        }
    }

    public function verifyPaymentOtpVia_mycash(Request $request){
        $gateway = new MyCashGatewayController();
        return $gateway->verifyOtp($request);
    }

    public function postPayment(Request $request, $domain='', $gateway = ''){
        if(!empty($gateway)){
            $function = 'postPaymentVia_'.$gateway;
            if(method_exists($this, $function)) {
                if(!empty($request->payment_form)){
                    $response = $this->$function($request); // call related gateway for payment processing
                    return $response;
                }
            }
            else{
                return $this->errorResponse("Invalid Gateway Request", 400);
            }
        }else{
            return $this->errorResponse("Invalid Gateway Request", 400);
        }
    }

    public function postPaymentVia_cashfree(Request $request){
        $gateway = new CashfreeGatewayController();
        return $gateway->createOrder($request);
    }
    public function postPaymentVia_easebuzz(Request $request){
        $gateway = new EasebuzzController();
        return $gateway->order($request);
    }

    public function postPaymentVia_vnpay(Request $request){
        $gateway = new VnpayController();
        return $gateway->order($request);
    }

    public function postPaymentVia_payu(Request $request){
        $gateway = new PayUGatewayController();
        return $gateway->purchase($request);
    }

    public function postPaymentVia_mycash(Request $request){
        $gateway = new MyCashGatewayController();
        return $gateway->purchase($request);
    }
    public function postPaymentVia_userede(Request $request){
        $gateway = new UseRedePaymentController();
        return $gateway->beforePayment($request);
    }
    public function postPaymentVia_openpay(Request $request){
        $gateway = new OpenpayPaymentController();
        return $gateway->beforePayment($request);
    }
}
