<?php

namespace App\Http\Controllers\Api\v1;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponser;
use Session,Auth,DB,Timezonelist,Log;
use App\Http\Traits\Giftcard\GiftCardTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\v1\{BaseController};
use App\Models\{GiftCard,UserGiftCard,User, Cart, ClientPreference, Client, ClientCurrency , Payment, PaymentOption};
use App\Http\Traits\StripeTrait;
use App\Http\Traits\{PaymentTrait};

class GiftcardController extends BaseController
{
    use ApiResponser,GiftCardTrait,StripeTrait,PaymentTrait;
    /**
     * getGiftCard api
     *
     * @param  mixed $request
     * @return void
     */
    public function getGiftCard(Request $request)
    {   
        try{
            $user = Auth::user();
            $now  = Carbon::now()->toDateTimeString();
            $GiftCard        = GiftCard::orderBy('id', 'asc')->whereDate('expiry_date', '>=', $now)
            ->with('giftCardTranslation',function($q)use($request){
                $q->where('language_id',$request->header('language'));
            })->get();
            $active_giftcard = $this->getUserActiveGiftCard();
            // return array
            $respons['allGiftCard']          =  $GiftCard;
            $respons['UserActiveGiftCard']   =  $active_giftcard;
            return $this->successResponse($respons, '', 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage( ), $e->getCode());
        }
    }
    
  

    

    /**
     * buy user giftCard.
     *
     * @return \Illuminate\Http\Response
     */

     public function selectgiftCard(Request $request)
     {
        $langId = Session::get('customerLanguage');
        $navCategories = $this->categoryNav($langId);
        $currency_id = Session::get('customerCurrency');
        $currencySymbol = Session::get('currencySymbol');
        $clientCurrency = ClientCurrency::where('currency_id', $currency_id)->first();
        $gift_card_id =$request->has('gift_card_id') ? $request->gift_card_id:$gift_card_id;
        $GiftCard  = GiftCard::with('giftCardTranslation',function($q)use($request){
            $q->where('language_id',$request->header('language'));
        })->where('id', $gift_card_id)->first();
        $code = $this->paymentOptionArray('GiftCard');
        $ex_codes = array('cod');
        $payment_options = PaymentOption::select('id', 'code', 'title', 'credentials')->whereIn('code', $code)->where('status', 1)->get();

        if ($GiftCard) 
        {
           foreach ($payment_options as $k => $payment_option) {
            if ((in_array($payment_option->code, $ex_codes)) || (!empty($payment_option->credentials))) {
                $payment_option->slug = strtolower(str_replace(' ', '_', $payment_option->title));
                if ($payment_option->code == 'stripe') {
                    $payment_option->title = 'Credit/Debit Card (Stripe)';
                } elseif ($payment_option->code == 'kongapay') {
                    $payment_option->title = 'Pay Now';
                } elseif ($payment_option->code == 'mvodafone') {
                    $payment_option->title = 'Vodafone M-PAiSA';
                } elseif ($payment_option->code == 'offline_manual') {
                    $json = json_decode($payment_option->credentials);
                    $payment_option->title = $json->manule_payment_title;
                } elseif ($payment_option->code == 'mycash') {
                    $payment_option->title = __('Digicel MyCash');
                } elseif ($payment_option->code == 'windcave') {
                    $payment_option->title = __('Windcave (Debit/Credit card)');
                } elseif ($payment_option->code == 'stripe_ideal') {
                    $payment_option->title = __('iDEAL');
                } elseif ($payment_option->code == 'authorize_net') {
                    $payment_option->title = __('Credit/Debit Card');
                } elseif ($payment_option->code == 'obo') {
                    $payment_option->title = __("MoMo, Airtel Money, Credit/Debit Cards by O'Pay");
                } elseif ($payment_option->code == 'livee') {
                    $payment_option->title = __("Livees");
                }
                $payment_option->title = __($payment_option->title);
                // unset($payment_option->credentials);
            } else {
                unset($payment_options[$k]);
            }
            return response()->json(["status" => "Success","GiftCard"=>$GiftCard,  "payment_options" => $payment_options, "currencySymbol" => $currencySymbol]);
          }
             
        } else {
            return response()->json(["status" => "Error", "message" => __("Invalid gift card")]);
        }
        
    }


    public function purchaseGiftCard(Request $request, $gift_card_id)
    {

        if( (isset($request->user_id)) && (!empty($request->user_id)) ){
            $user = User::find($request->user_id);
        }else{
            $user = Auth::user();
        }
        $GiftCard = GiftCard::with('giftCardTranslation',function($q)use($request){
            $q->where('language_id',$request->header('language'));
        })->where('id', $gift_card_id)->first();
        // pr($GiftCard);

        $sendToMail = (isset($request->email) && !empty($request->email) ) ? $request->email : '';
        $sendToName = (isset($request->name) && !empty($request->name) ) ?  $request->name : '';
        
        $senderData['send_card_to_name'] = $request->has($request->name) ? $request->name :'';
        $senderData['send_card_to_email'] = $request->has($request->email)?$request->email :'';
        $senderData['send_card_to_mobile'] = $request->has($request->mobile)?$request->mobile :'';
        $senderData['send_card_to_address'] = $request->has($request->address)? $request->address: '';
        $senderData['send_card_is_delivery'] = $request->has($request->is_delivery)? $request->is_delivery: '';

       // pr( $request->all());
        if( $GiftCard ){
            try{
                $code =$this->getGiftCardCode($GiftCard->title);
                $UserGiftCard               = new UserGiftCard();
                $UserGiftCard->user_id      = $user->id;
                $UserGiftCard->gift_card_id = $GiftCard->id;
                $UserGiftCard->amount       = $GiftCard->amount;
                $UserGiftCard->expiry_date  = $GiftCard->expiry_date;
                $UserGiftCard->gift_card_code = $code;
                $UserGiftCard->buy_for_data = json_encode($request->senderData);
                $UserGiftCard->save();

            }
            catch (Excepion $e)
            {
                return $e->getMessage();
            }
            if($sendToMail != ''){
                $currency_id = isset($user->currency) ? $user->currency : 1;
               
                $clientCurrency = ClientCurrency::where('currency_id', $currency_id )->first();
                $currSymbol = (isset($clientCurrency->currency->symbol)) ? $clientCurrency->currency->symbol : '$';
                $GiftCard->userCode =  $code;
                $this->GiftCardMail($sendToMail,$sendToName, $GiftCard ,$user ,$currSymbol);
            }
            $payment                        = new Payment;
            $payment->user_id               = $user->id;
            $payment->balance_transaction   = $request->amount;
            $payment->transaction_id        = $request->transaction_id;
            $payment->reference_table_id    = $UserGiftCard->id;
            $payment->payment_option_id     = $request->payment_option_id;
            $payment->date                  = Carbon::now()->format('Y-m-d');
            $payment->type                  = 'giftCard';
            $payment->save();
            
            $message = __('Your Gift Card has been activated successfully.');
            Session::put('success', $message);
            return $this->successResponse('', $message);
            
        }else{
            return $this->errorResponse(__('Invalid Data'), 402);
        }
    }

    /**
     * buy user postGiftCardLisTCart.
     *
     * @return \Illuminate\Http\Response
     */
    public function postGiftCardLisTCart(Request $request){
         try {
            $user = Auth::user();
            $langId   = Session::has('customerLanguage') ? Session::get('customerLanguage') : 1;
            $giftCard = new \Illuminate\Database\Eloquent\Collection;
            $giftcardList = $this->getUserActiveGiftCard();
            $currency_id = Session::get('customerCurrency');
            $clientCurrency = ClientCurrency::where('currency_id', $currency_id)->first();
            $returnHTML = view('frontend.cart.giftCard')->with(['giftcardList'=>$giftcardList, 'clientCurrency'=>$clientCurrency])->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
           
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

        
    /**
     * postVerifyGiftCardCode
     *
     * @param  mixed $request
     * @return void
     */
    public function postVerifyGiftCardCode(Request $request){
        //try {
        //     $user = Auth::user();
        //     $now = Carbon::now()->toDateTimeString();
        //     $cart_detail = Cart::where('id', $request->cart_id)->first();
        //     if(!$cart_detail){
        //         return $this->errorResponse('Invalid Cart Id', 422);
        //     }
           
        //     $giftcard = UserGiftCard::with('giftCard')->whereHas('giftCard',function ($query) use ($now,$request){
        //         return  $query->whereDate('expiry_date', '>=', $now)->where('name',$request->giftCardCoe);
        //     })->where(['is_used'=>'0','user_id'=>$user->id])->first();

            
        //     if($giftcard){
        //         if($cart_detail->gift_card_id ==  $giftcard->gift_card_id){
        //             return $this->errorResponse('Gift Card already applied.', 422);
        //         }
        //         $cart_detail->gift_card_id = $giftcard->gift_card_id;
        //         $cart_detail->save();
        //         return $this->successResponse('', 'Gift Card Used Successfully.', 200);
        //     }
        //     return $this->errorResponse('Invalid gift Card', 422);
           
        // } catch (Exception $e) {
        //     return $this->errorResponse($e->getMessage(), $e->getCode());
        // }
        try {
            $user = Auth::user();
            $now = Carbon::now()->toDateTimeString();
           
            $cart_detail = Cart::where('id', $request->cart_id)->first();
            if(!$cart_detail){
                return $this->errorResponse('Invalid Cart Id', 422);
            }
          
            $giftcard = UserGiftCard::with('giftCard')->whereHas('giftCard',function ($query) use ($now,$request){
                return  $query->whereDate('expiry_date', '>=', $now);
            })->where(['is_used'=>'0','gift_card_code' => $request->gift_card_code])->first(); //,'gift_card_code'=>$request->giftCardCode
          
            if($giftcard){
                if($cart_detail->gift_card_id ==  $giftcard->id){
                    return $this->errorResponse('Gift Card already applied.', 422);
                }
                $cart_detail->gift_card_id = $giftcard->id;
                $cart_detail->user_gift_code = $giftcard->gift_card_code;
                
                
                $cart_detail->save();
                return $this->successResponse($giftcard, 'Gift Card Used Successfully.', 200);
            }
            return $this->errorResponse('Invalid gift Card', 422);
           
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }



    }

    public function RemoveGiftCardCode(Request $request){
        try {
             $user = Auth::user();
             
             $cart_detail = Cart::where(['id'=>$request->cart_id,'user_id'=>  $user->id ])->first();
             \Log::info( ['cart_detail' => $cart_detail->giftCard] );
             if(!$cart_detail){
                 return $this->errorResponse('Invalid Cart Id', 422);
             }
          
            if($cart_detail){
                $cart_detail->gift_card_id = null;
                $cart_detail->user_gift_code     = null;
                $cart_detail->giftCard->amount = $cart_detail->giftCard->used_amount ;
                $cart_detail->giftCard->used_amount = 0;
                $cart_detail->giftCard->save();
                $cart_detail->save();
              return $this->successResponse('', 'Gift Card Delete Successfully.', 200);
            }
            return $this->errorResponse('Invalid gift Card Id', 422);
            
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
     }
   
}

