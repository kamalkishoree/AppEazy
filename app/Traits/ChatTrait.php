<?php
namespace App\Traits;
use App\Model\{ClientPreference,Agent,Order};
use GuzzleHttp\Client as GCLIENT;
use Log;
use App\Services\FirebaseService;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Http\Request;

trait ChatTrait{
    
    /**
     * sendNotificationToOrder
     *
     * @param  mixed $request
     * @return void
     */
    public function sendNotificationToOrder($request)
    {
       
        /**dispacth noti */
        $data = $request->all();
        $this->getOrderUrl($request->order_vendor_id,$request->order_number,$request->vendor_id,$data);
        /**end */
    }



    
    /**
     * sendNotification_to_agent
     *
     * @param  mixed $request
     * @return void
     */
    public function sendNotification_to_agent($request)
    {
        // $ag ='all_agentids';

        // if(@$request->all()['web']=="true"){
        //     $ag ='user_ids';
        // }
        // $result = array_values(array_column($request->all()[$ag], 'auth_user_id'));
        // $client_preferences = ClientPreference::select('fcm_server_key','favicon')->first();
        // $devices = Agent::whereNotNull('device_token')->whereIn('id',$result)->pluck('device_token');
        //     $data = [
        //         "registration_ids" => $devices,
        //         "notification" => [
        //             "title" => $request->username,
        //             "body"  => $request->text_message,
        //             'sound' => "default",
        //             //"icon"  => (!empty($client_preferences->favicon)) ? $client_preferences->favicon['proxy_url'] . '200/200' . $client_preferences->favicon['image_path'] : '',
        //             "android_channel_id" => "default-channel-id"
        //         ],
        //         "data" => [
        //             "title" => $request->username,
        //             "room_id"=>$request->roomId,
        //             "room_id_text"=>$request->roomIdText,
        //             "body"  => $request->text_message,
        //             'data'  => 'chat_text',
        //             'type'  => "",
        //         ],
        //         "priority" => "high"
        //     ];
                      
        //     //$response = sendFcmCurlRequest($data);
        //     $fcm_server_key = ($client_preferences->fcm_server_key !='') ? $client_preferences->fcm_server_key :  env('FCM_SERVER_KEY');
        //     if (!empty($fcm_server_key )) {
        //         $headers = [
        //             'Authorization: key='.$fcm_server_key ,
        //             'Content-Type: application/json',
        //         ];
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        //         curl_setopt($ch, CURLOPT_POST, true);
        //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //         $result = curl_exec($ch);
        //         // if ($result === FALSE) {
        //         //     die('Oops! FCM Send Error: ' . curl_error($ch));
        //         // }
        //         curl_close($ch);
        //         $result = json_decode($result); 
        //         return $result;
        //     }







            $ag ='all_agentids';
            if(@$request->all()['web']=="true"){
                $ag ='user_ids';
            }
            $data = $request->all();
            $order  = Order::where('order_number',$request->order_number)->first();
            Log::info('agent_idagent_idagent_idagent_idagent_idagent_idagent_idagent_idagent_id'.$order->driver_id);
            $agent_id = [];
            $agent_id [] = $order->driver_id;
            Log::info(['agent_id'=> $agent_id]);

            $result =  $agent_id;
            Log::info(['resultresult'=> $result]);

            $client_preferences = ClientPreference::select('fcm_server_key','favicon')->first();
            $devices = Agent::whereNotNull('device_token')->whereIn('id',$result)->pluck('device_token');
            Log::info(['devicesdevicesdevices'=> $devices]);
            $data = [
                "registration_ids" => $devices,
                "notification" => [
                    "title" => $request->username,
                    "body"  => $request->text_message,
                    'sound' => "default",
                    //"icon"  => (!empty($client_preferences->favicon)) ? $client_preferences->favicon['proxy_url'] . '200/200' . $client_preferences->favicon['image_path'] : '',
                    "android_channel_id" => "default-channel-id"
                ],
                "data" => [
                    "title" => $request->username,
                    "room_id"=>$request->roomId,
                    "room_id_text"=>$request->roomIdText,
                    "body"  => $request->text_message,
                    'data'  => 'chat_text',
                    'type'  => $request->chat_type,
                ],
                "priority" => "high"
            ];
            $response = FirebaseService::sendNotification($data);

















    }
    
    /**
     * getDispatchDomain
     *
     * @return void
     */
    public function getDispatchDomain()
    {
        $preference = ClientPreference::first();
        return $preference;
       
    }

    
    /**
     * getOrderUrl
     *
     * @param  mixed $order_vendor_id
     * @param  mixed $order_id
     * @param  mixed $vendor_id
     * @param  mixed $postdata
     * @return void
     */
    public function getOrderUrl($order_vendor_id,$order_id,$vendor_id,$postdata)
    {
       
        $Order = Order::where(['order_number' => $order_id])->first();   
        $call_back_url = $order_back_url  = '';   
        if($Order){
            $call_back_url = $Order->call_back_url;   
        }
        $serverUrl = parse_url($call_back_url);
        
        if(is_array($serverUrl)){
            $order_back_url = $serverUrl['scheme'].'://'.$serverUrl['host'].'/sendNotificationToUserByDispatcher';
        }
        // Log::info($order_back_url);
        // Log::info($postdata);
        $client = new GClient([
            'headers' => [
                'content-type' => 'application/json'
            ]
        ]);
        $res = $client->post(
            $order_back_url,
            ['form_params' => ($postdata)]
        );
        $response = json_decode($res->getBody(), true);
        return response()->json([ 'notiFY'=>$response , 'status' => 200, 'message' => __('sent!!!')]);
    }




    public function signAws(Request $request)
    {
            // Retrieve environment variables
            $accessKeyId = env('AWS_ACCESS_KEY_ID');
            $secretAccessKey = env('AWS_SECRET_ACCESS_KEY');
            $region = env('AWS_DEFAULT_REGION');
            $bucketName = env('AWS_BUCKET');
            
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

    
}
