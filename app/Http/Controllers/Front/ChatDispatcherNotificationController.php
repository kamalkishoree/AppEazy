<?php

namespace App\Http\Controllers\Front;

use DB;
use Log;
use Auth;
use Session;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Front\FrontController;
use App\Http\Traits\ChatTrait;
use App\Http\Traits\GlobalFunction;
use Illuminate\Support\Facades\Http;
// use App\Models\{Order,OrderVendor,UserDevice,ClientPreference, Product,UserVendor};

use App\Models\{Client, Order,OrderVendor, UserVendor, UserDevice,ClientPreference, LoyaltyCard,OrderProductRating};

class ChatDispatcherNotificationController extends FrontController
{
    use GlobalFunction;
    use ChatTrait;
    /**
     * Display a listing of the country resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $client_data;
    public function __construct()
    {
        
      
    }

    public function sendNotificationToUserByDispatcher(Request $request){
       
        try {
            $notiFY = $this->sendNotification($request,'from_dispatcher');
            return response()->json([ 'notiFY'=>$notiFY , 'status' => true, 'message' => __('sent!!!')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'notiFY' => [] , 'message' => __('No Data found !!!')]);
        }

    }
    public function sendNotificationToUser(Request $request){
        try {
            $notiFY = $this->sendNotification($request,'');
            return response()->json([ 'notiFY'=>$notiFY , 'status' => true, 'message' => __('sent!!!')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'notiFY' => [] , 'message' => __('No Data found !!!')]);
        }

    }


    public function sendNotificationToDriver(Request $request,$from='')
    {
    
        $data = $request->all();
        $type = @$request->chat_type;
        $order_vendor_id ="";
        $order_id ="";
        $from_message = '';
        $to_message = '';
        $vendor_id = $request->has('vendor_id')?$request->vendor_id:"N/A";

        $message = isset($request->text_message)?$request->text_message :'';
        if($from=='from_dispatcher'){
            $username =  $data['username'];
            $type = $request->chat_type;
            $removeAuth = (isset($request->all()['user_ids']))  ? array_values(array_column($request->all()['user_ids'], 'auth_user_id')): [];
            
        } else{
            $username =  Auth::user()->name;
            $auid =  Auth::user()->id;
            $result = (isset($request->all()['user_ids'])) ? array_values(array_column($request->all()['user_ids'], 'auth_user_id')):[];
            $removeAuth = $result;
            $type = $request->chat_type;
            if(@$data['auth_id']==$auid){
                $removeAuth = array_values(array_diff($result, array($auid)));
            }
             /**dispacth noti */
            //  if(@$data['order_vendor_id']!=''){
            //     $this->getDispacthUrl($data['order_vendor_id'],$data['order_id'],$data['vendor_id'],$data);
            //    
        }
        $client_preferences = ClientPreference::select('fcm_server_key','favicon')->first();
        $devices= UserDevice::whereNotNull('device_token')->whereIn('user_id',$removeAuth)->pluck('device_token') ?? [];

         if($request->has('dine_in_type') && $request->dine_in_type ==  'takeaway')
         {         
            if($request->has('vendor_id'))
            {
                $order_vendor_id = $request->order_vendor_id;
                $vendor_id =$request->has('vendor_id')?$request->vendor_id:'';
                $user_ids = UserVendor::where('vendor_id',$request->vendor_id)->pluck('user_id')->toArray();
                $devices= UserDevice::whereNotNull('device_token')->whereIn('user_id',$user_ids)->pluck('device_token') ?? [];
                \Log::info(['user_idsuser_idsuser_ids'=>$user_ids]);
                $message = $username. ' is reached to pick his takeaway order.';
            }
         }

        elseif($request->has('order_vendor_id') && $request->has('vendor_id') && $request->chat_type != "agent_to_user")
        {
            $order_vendor_id = $request->order_vendor_id;
            $vendor_id =$request->has('vendor_id')?$request->vendor_id:'';
            $order = OrderVendor::where('id',$request->order_vendor_id)->first();
            // pr($data);
            if(empty($order))
            {
                $order = OrderVendor::where('order_id',$request->order_vendor_id)->first();  
            }
            $user_ids[] = $order->user_id;
            $type = $request->chat_type;
            $UserVendor_ids = UserVendor::where('vendor_id',$order->vendor_id)->pluck('user_id')->toArray();
            $order_id = $order->order_id;
            $vendor_id = $order->vendor_id;
          

            // \Log::info(['$user_ids_before' => $user_ids]);
           
            // \Log::info(['$UserVendor_ids' => $UserVendor_ids]); 
            $user_ids = array_merge($UserVendor_ids,$user_ids);
           
            // \Log::info(['$user_ids_after' => $user_ids]);
            // \Log::info(['$UserVendor_ids' => $UserVendor_ids]);
            
            $login_user [] = auth()->user()->id;
              //\Log::info(['$user_ids' => $user_ids]);
            $removeAuth = array_diff($user_ids,$login_user);
            //\Log::info(['$removeAuth' => $removeAuth]);
            
            if(in_array($vendor_id,$removeAuth))
            {
                \Log::info('vendorrrrrrrrrrrrrrr');
               $from_message = 'from_vendor';
               $to_message = 'to_user';
            }
            elseif(in_array($order->user_id,$removeAuth)){
                \Log::info('userrrrrrrrrrrrrrrrrrr');
                $from_message = 'from_user';
                $to_message = 'to_vendor';
            }
            $user_devices  = UserDevice::whereIn('user_id',$removeAuth)->pluck('device_token')->toArray() ?? [];
            $devices =$user_devices;
        }


        
        else{
            $data['all_agentids'] =[];
            if(isset($data['user_ids']))
            {
                foreach($data['user_ids'] as $agent)
                {
                    if($agent['user_type']=="agent")
                    {
    
                        $data['all_agentids'][] = $agent['user_id'];
                        $data['order_vendor_id']= $agent['order_vendor_id'];
                        $data['vendor_id'] =$agent['vendor_id'];
                    }
                  
                }
            }
        

            if(isset($data['chat_type']) && $data['chat_type']== 'user_to_agent')
            {
             
                if(isset($data['order_vendor_id']) && isset($data['vendor_id'])){
                    if(!empty($data['vendor_id']) || !empty($data['order_vendor_id'])){
                        $order = OrderVendor::select('order_id')->where('id',$data['order_vendor_id'])->first();
                        $data['order_number'] = !empty($order)?$order->orderDetail->order_number:'';
                        $data_arr['order_id'] = !empty($order)?$order->order_id:'';
                      return  $this->getDispacthUrl($data['order_vendor_id'],$order->order_id,$data['vendor_id'],$data);
                    }
                }
            }
        }
        
        if (!empty($devices) && !empty($client_preferences->fcm_server_key)) {
            if (!empty($devices) && !empty($client_preferences->fcm_server_key)) {
                $data = [
                    "registration_ids" => $devices,
                    "notification" => [
                        "title" => $username,
                        "body"  => $message,
                        'sound' => "default",
                        "icon"  => (!empty($client_preferences->favicon)) ? $client_preferences->favicon['proxy_url'] . '200/200' . $client_preferences->favicon['image_path'] : '',
                        "android_channel_id" => "default-channel-id"
                    ],
                    "data" => [
                        "title" => $username,
                        "room_id"=>$request->roomId,
                        "room_id_text"=>$request->roomIdText,
                        "body"  => $message,
                        'data'  => 'chat_text',
                        'type'  => $type,
                        'order_vendor_id' => $order_vendor_id,
                        "order_id" =>$order_id,
                        "vendor_id" =>$vendor_id,
                        'to_message' => $to_message,
                        'from_message' => $from_message,
                    ],
                    "priority" => "high"
                ];

                \Log::info(['fcm_data' =>$data]);

                $response = sendFcmCurlRequest($data);
                return $result;
            }
          
        }
      }
    }

