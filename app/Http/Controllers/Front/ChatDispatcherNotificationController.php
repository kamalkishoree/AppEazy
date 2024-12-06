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


        // pr($request->all());
        $data = $request->all();
        $type = @$request->chat_type;
        $order_vendor_id ="";
        $order_id ="";
        $from_message = '';
        $to_message = '';
        $vendor_id = $request->has('vendor_id')?$request->vendor_id:"N/A";
        $message = isset($request->text_message)?$request->text_message :'';
        $devices = [];
        $username= '';
        $user = auth()->user();
        if( !$username)
        {
            $username=  $user->name;
        }
        $client_preferences = ClientPreference::select('fcm_server_key','favicon')->first();
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

            if(isset($data['chat_type']) && $data['chat_type']== 'user_to_vendor')
            {
                $UserVendor_ids = UserVendor::where('vendor_id',$data['vendor_id'])->pluck('user_id')->toArray();
                $user_devices  = UserDevice::whereIn('user_id',$UserVendor_ids)->pluck('device_token')->toArray() ?? [];
                $devices =$user_devices;

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
                return $response;
            }
          
        }
      }
    }

