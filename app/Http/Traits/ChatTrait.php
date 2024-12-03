<?php
namespace App\Http\Traits;
use App\Models\{Order,OrderVendor,UserDevice,ClientPreference, Product,UserVendor};
use Auth;
use GuzzleHttp\Client as GCLIENT;
use Log;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

trait ChatTrait{


     # get prefereance if last mile on or off and all details updated in config
     public function getDispatchDomain()
     {
         $preference = ClientPreference::first();
         return $preference;
        
     }
    
    /**
     * OrderVendorDetail
     *
     * @param  mixed $request
     * @return void
     */
    public function OrderVendorDetail($request)
    {
        $data = $request->all();
        $order_vendor_id = $data['order_vendor_id'];
        $order_id = $data['order_id'];
        $order = Order::with(array(
            'vendors' => function ($query) use ($order_vendor_id) {
                $query->where('id', $order_vendor_id);
            },'vendors.vendor'
        ))->findOrFail($order_id);
        return  $order;
    }

    /**
     * OrderVendorDetail
     *
     * @param  mixed $request
     * @return void
     */
    public function ProductDetail($request)
    {
        $data = $request->all();
        $pid  = $data['product_id'];
        $product = Product::with([
        'category.categoryDetail', 'variant.media.pimage.image', 'vendor', 'media.image', 'related', 'upSell', 'crossSell']);
   
        $product = $product->select('id', 'title', 'sku', 'url_slug', 'weight', 'weight_unit', 'vendor_id', 'is_new', 'is_featured', 'is_physical', 'has_inventory', 'has_variant', 'sell_when_out_of_stock', 'requires_shipping', 'Requires_last_mile', 'averageRating','minimum_order_count','batch_count','minimum_duration','minimum_duration_min','additional_increments','additional_increments_min','buffer_time_duration','buffer_time_duration_min',  'is_long_term_service','service_duration','latitude','longitude','address');
   
        $product = $product->where('id', $pid)
        ->first();
        return  $product;
    }
    
    /**
     * sendNotification
     *
     * @param  mixed $request
     * @param  mixed $from
     * @return void
     */
    public function sendNotification($request,$from='')
    {
        \Log::info(['request',$request->all()]);
        $chat_type = $request->has('chat_type')?$request->chat_type:'';
        $data = $request->all();
        if($from=='from_dispatcher'){
            $username =  $data['username'];
            $removeAuth = (isset($request->all()['user_ids']))  ? array_values(array_column($request->all()['user_ids'], 'auth_user_id')): [];
        } else{
            $username =  Auth::user()->name;
            $auid =  Auth::user()->id;
            $result = (isset($request->all()['user_ids'])) ? array_values(array_column($request->all()['user_ids'], 'auth_user_id')):[];
            $removeAuth = $result;
            if(@$data['auth_id']==$auid){
                $removeAuth = array_values(array_diff($result, array($auid)));
            }
             /**dispacth noti */
             if(@$data['order_vendor_id']!=''){
                $this->getDispacthUrl($data['order_vendor_id'],$data['order_id'],$data['vendor_id'],$data);
             }
            
            /**end */
        }
       
        $client_preferences = ClientPreference::select('fcm_server_key','favicon')->first();
        $devices            = UserDevice::whereNotNull('device_token')->whereIn('user_id',$removeAuth)->pluck('device_token') ?? [];
        if (!empty($devices) && !empty($client_preferences->fcm_server_key)) {
            $data = [
                "registration_ids" => $devices,
                "notification" => [
                    "title" => $username,
                    "body"  => $request->text_message,
                    'sound' => "default",
                    "icon"  => (!empty($client_preferences->favicon)) ? $client_preferences->favicon['proxy_url'] . '200/200' . $client_preferences->favicon['image_path'] : '',
                    "android_channel_id" => "default-channel-id"
                ],
                "data" => [
                    "title" => $username,
                    "room_id"=>$request->roomId,
                    "room_id_text"=>$request->roomIdText,
                    "body"  => $request->text_message,
                    'data'  => 'chat_text',
                    'type'  => $chat_type
                ],
                "priority" => "high"
            ];
                      
            $response = sendFcmCurlRequest($data);
            $result = json_decode($response); 
            return $result;
        }
    }
    
    /**
     * getDispacthUrl
     *
     * @param  mixed $order_vendor_id
     * @param  mixed $order_id
     * @param  mixed $vendor_id
     * @param  mixed $postdata
     * @return void
     */
    public function getDispacthUrl($order_vendor_id,$order_id,$vendor_id,$postdata)
    {

     $checkdeliveryFeeAdded = OrderVendor::with('LuxuryOption')->where(['order_id' => $order_id, 'vendor_id' => $vendor_id])->first();      
        $luxury_option_id = isset($checkdeliveryFeeAdded) ? @$checkdeliveryFeeAdded->LuxuryOption->luxury_option_id : 1;
        $dispatchDomain = $this->getDispatchDomain();
      
        /// luxury option 8 ( static ) for appointment you can check it on luxuryOptionSeeder
        if ($luxury_option_id == 8) { // only for appointment type 
                $dispatch_domain=[
                    'service_key'      => $dispatchDomain->appointment_service_key,
                    'service_key_code' => $dispatchDomain->appointment_service_key_code,
                    'service_key_url'  => $dispatchDomain->appointment_service_key_url,
                ];
            
        }elseif ($luxury_option_id == 6) { // only for on_demand type         
            if($dispatchDomain && $dispatchDomain != false){
               
                $dispatch_domain=[
                    'service_key'      => $dispatchDomain->dispacher_home_other_service_key,
                    'service_key_code' => $dispatchDomain->dispacher_home_other_service_key_code,
                    'service_key_url'  => $dispatchDomain->dispacher_home_other_service_key_url,
                 
                ];
            }
        } else{
            $dispatch_domain=[
                'service_key'      => $dispatchDomain->delivery_service_key,
                'service_key_code' => $dispatchDomain->delivery_service_key_code,
                'service_key_url'  => $dispatchDomain->delivery_service_key_url,
                
              
            ];
        }
       $this->hitDispacthHook($dispatch_domain,$postdata);
       
    }    
    /**
     * hitDispacthHook
     *
     * @param  mixed $dispatch_domain
     * @param  mixed $postdata
     * @return void
     */
    public function hitDispacthHook($dispatch_domain,$postdata){
      \Log::info(['postdata'=>$postdata]);
        if ($dispatch_domain && $dispatch_domain != false) {
                $client = new GClient([
                    'headers' => [
                        'personaltoken' => $dispatch_domain['service_key'],
                        'shortcode' => $dispatch_domain['service_key_code'],
                        'content-type' => 'application/json'
                    ]
                ]);
                
                $url = $dispatch_domain['service_key_url'];
                // Log::info($url);
                // Log::info($postdata);
                $res = $client->post(
                    $url . '/api/chat/sendNotificationToAgent',
                    ['form_params' => ($postdata)]
                );
                $response = json_decode($res->getBody(), true);
                Log::info('responseresponseresponseresponseresponseresponse');

                Log::info($response);
                return $response;
        } else{
            return response()->json(['status' => false, 'notiFY' => [] , 'message' => __('No Data found!!!')]);
        }
    }

    public function signAws(Request $request)
    {
       
            // Retrieve environment variables
            $accessKeyId = env('AWS_ACCESS_KEY_ID_CHAT');
            $secretAccessKey = env('AWS_SECRET_ACCESS_KEY_CHAT');
            $region = env('AWS_DEFAULT_REGION_CHAT');
            $bucketName = env('AWS_BUCKET_CHAT');
            
            // Get the file path from the request
            $fileName = $request->input('filename');

            // Initialize the S3 Client
            $s3Client = new S3Client([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            try {
                // Set the file content in the 'Body' parameter
                $cmd = $s3Client->getCommand('PutObject', [
                    'Bucket' => $bucketName,
                    'Key' => $fileName,
                    'ACL' => 'public-read',
                ]);
                // Generate the presigned request
                $request = $s3Client->createPresignedRequest($cmd, '+2 hour');
                $signedUrl = (string) $request->getUri();
                \Log::info($signedUrl);
                return response()->json([
                    'url' => $signedUrl,
                ]);

            } catch (AwsException $e) {
                // Handle any errors
                return response()->json(['error' => $e->getMessage()], 500);
            }
    }

    public function sendNotificationNew($request,$from='')
    {
        \Log::info(['requestall'=>$request->all()]);
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
            \Log::info('hereeeeeeeeeeeeeee');
            $order_vendor_id = $request->order_vendor_id;
            $vendor_id =$request->has('vendor_id')?$request->vendor_id:'';
            $order = OrderVendor::where('id',$request->order_vendor_id)->first();
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
            if(isset($data['chat_type']) && $data['chat_type']== 'agent_to_user')
            {
                \Log::info(['driveeeeeeeeeeeeeeeeeeeeeerrrr'=>$data['all_agentids'][0]]);
                $data_arr = @$data['all_agentids'][0];
               
                \Log::info(['requestrequestrequestrequestrequestrequest'=>$request->all()]);

                \Log::info(['data_arrdata_arrdata_arrdata_arr'=>$data_arr]);

                if(isset($data['order_vendor_id']) && isset($data['vendor_id'])){
                    \Log::info(['kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk'=>$data_arr]);

                    if(!empty($data['vendor_id']) || !empty($data['order_vendor_id'])){
                        $order = OrderVendor::select('order_id')->where('id',$data['order_vendor_id'])->first();
                        $data['order_number'] = !empty($order)?$order->orderDetail->order_number:'';
                        $data_arr['order_id'] = !empty($order)?$order->order_id:'';
                        $this->getDispacthUrl($data['order_vendor_id'],$order->order_id,$data['vendor_id'],$data);
                    }
                }
            }
        }
        \Log::info(['devicesdevicesdevicesdevicesdevicesdevicesdevicesdevicesdevicesdevices'=>$devices]);
        
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
