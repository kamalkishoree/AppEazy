<?php

namespace App\Services;

use GuzzleHttp\Client;

use GuzzleHttp\Exception\RequestException;

use App\Models\{ClientPreference, Order};

class FirebaseService

{

    //public $projectId;

    protected $client;

    //protected $serviceAccount;

    public function __construct()

    {

        //$this->projectId = config('services.firebase.project_id');

        $this->client = new Client();

        //$this->serviceAccount = json_decode(file_get_contents(public_path("firebase/fcm.json")), true);

    }

    public function getAccessToken()

    {

        $client = new Client();

        $serviceAccount = json_decode(file_get_contents(public_path("firebase/fcm.json")), true);

        //pr($serviceAccount);die;

        $now = time();

        $payload = [

            'iss' => $serviceAccount['client_id'],

            'sub' => $serviceAccount['client_id'],

            'aud' => 'https://oauth2.googleapis.com/token',

            'iat' => $now,

            'exp' => $now + 3600,

            'scope' => 'https://www.googleapis.com/auth/firebase.messaging'

        ];

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];

        $base64UrlHeader = Self::base64UrlEncode(json_encode($header));

        $base64UrlPayload = Self::base64UrlEncode(json_encode($payload));

        $signature = '';

        openssl_sign($base64UrlHeader . '.' . $base64UrlPayload, $signature, $serviceAccount['private_key'], 'sha256');

        $base64UrlSignature = Self::base64UrlEncode($signature);

        $jwt = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;

        try {

            $response = $client->post('https://oauth2.googleapis.com/token', [

                'form_params' => [

                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',

                    'assertion' => $jwt,

                ],

            ]);

            $data = json_decode($response->getBody(), true);

            return $data['access_token'];

        } catch (RequestException $e) {

            // Handle the error appropriately

            return null;

        }

    }

    public function sendNotification($data) //$token, $title, $body

    {

        $client = new Client();

        $preference = ClientPreference::select('fcm_project_id')->first();

        if (!$preference) {

            \Log::error('FCM Send Error: FCM project ID not found in database.');

            return false;

        }

        $projectId = $preference->fcm_project_id;

        \Log::info('projectId');

        \Log::info($projectId);

        \Log::info('projectId');

        //$url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        //$accessToken = $this->getAccessToken();

        // $accessToken = Self::getAccessToken();

        $accessToken = getFcmOauthToken();

        \Log::info('accessToken');
        \Log::info($accessToken);
        if (!$accessToken) {

            return ['error' => 'Unable to fetch access token'];

        }

        // $data = [

        //     'token' => $devices[0],

        //     'notification' => [

        //         'title' => $notification_content->subject,

        //         'body'  => $body_content,

        //         // 'sound' => "default",

        //         // "icon" => (!empty($client_preferences->favicon)) ? $client_preferences->favicon['proxy_url'] . '200/200' . $client_preferences->favicon['image_path'] : '',

        //         //'click_action' => '',

        //         //"android_channel_id" => "default-channel-id",

        //         //"redirect_type" => $redirect_URL['type']

        //     ],

        //     'android' => [

        //         "priority" => 'HIGH',

        //         'notification' => [

        //             //'title' => $title,

        //             'icon' => 'myicon',

        //             'sound' => "notification.wav",

        //             'click_action' => '',

        //             'color' => '#ff0000',

        //             'channel_id' => "default-channel-id",

        //         ],

        //     ],

        //     'data' => [

        //         'title' => $notification_content->subject,

        //         'body'  => $body_content,

        //         "type" => "order_status_change",

        //         "order_id" =>"{$orderData->id}",

        //         "vendor_id" =>$orderData->ordervendor->vendor_id ?? '',

        //         "order_status" =>"{$order_status_id}",

        //         "redirect_type" => "{$redirect_URL['type']}"

        //     ],

        //     //"priority" => "high"

        // ];

        // \Log::info( $data );

        // $newData = [];

        // if (is_array($data['registration_ids']) && count($data['registration_ids']) > 1) {

        //     $newData['token'] = $data['registration_ids']; // Handle multiple tokens

        // } else {

        //     $newData['token'] = is_array($data['registration_ids']) ? $data['registration_ids'][0] : $data['registration_ids']; // Handle single token

        // }

        // $newData['notification'] = [];

        // foreach ($data['notification'] as $key => $value) {

        //     if (!in_array($key, ['sound', 'icon', 'click_action', 'android_channel_id', 'redirect_type'])) {

        //         $newData['notification'][$key] = $value;

        //     }

        // }

        // // Add the android section with nested notification settings

        // $newData['android'] = [

        //     'priority' => $data['priority'] ?? 'HIGH',

        //     'notification' => [

        //         'icon' => $data['icon'] ?? '',

        //         'sound' => $data['sound'] ?? '',

        //         'click_action' => $data['click_action'] ?? '',

        //         'channel_id' => $data['android_channel_id'] ?? '',

        //     ],

        // ];

        // // Process the data section, converting specific fields to strings

        // $newData['data'] = [];

        // foreach ($data['data'] as $key => $value) {

        //     //if (in_array($key, ['order_id', 'order_status', 'redirect_type'])) {

        //         $newData['data'][$key] = (string)$value;

        //     //} else {

        //         //$newData['data'][$key] = $value;

        //     //}

        // }

        // Output the new data array

        //print_r($newData);

        try {

            $messages = [];

            foreach ($data['registration_ids'] as $token) {

                // $message = [

                //     'token' => $token,

                //     'notification' => [

                //         'title' => $title,

                //         'body' => $body,

                //         'sound' => $sound,

                //         'icon' => $icon,

                //     ],

                //     'data' => $data,

                // ];

                $message['token'] = $token;

                foreach ($data['notification'] as $key => $value) {

                    if (!in_array($key, ['sound', 'icon', 'click_action', 'android_channel_id', 'redirect_type'])) {

                        $message['notification'][$key] = $value;

                    }

                }

                $message['android'] = [

                    'priority' => $data['priority'] ?? 'HIGH',

                    'notification' => [

                        'icon' => $data['notification']['icon'] ?? '',

                        'sound' => $data['notification']['sound'] ?? '',

                        'click_action' => $data['click_action'] ?? '',

                        'channel_id' => $data['notification']['android_channel_id'] ?? '',

                    ],

                ];

        

                // Process the data section, converting specific fields to strings

                //$newData['data'] = [];

                foreach ($data['data'] as $key => $value) {

                    //if (in_array($key, ['order_id', 'order_status', 'redirect_type'])) {

                        $message['data'][$key] = (string)$value;

                    //} else {

                        //$newData['data'][$key] = $value;

                    //}

                }

                //$messages[] = $message;

                try {

                    $response = $client->post($url, [

                        'headers' => [

                            'Authorization' => 'Bearer ' . $accessToken,

                            'Content-Type' => 'application/json',

                        ],

                        'json' => [

                            'validate_only' => false,

                            'message' => $message,

                        ],

                    ]);

    

                    $results[] = [

                        'status' => 'fulfilled',

                        'body' => (string) $response->getBody()

                    ];

                } catch (RequestException $e) {

                    $results[] = [

                        'status' => 'rejected',

                        'reason' => $e->getMessage()

                    ];

                }

            }

            return $results;

            // $promises = function() use ($client, $messages, $url) {

            //     foreach ($messages as $message) {

            //         yield $client->requestAsync('POST', $url, [

            //             'json' => [

            //                 'validate_only' => false,

            //                 'message' => $message

            //             ],

            //         ]);

            //     }

            // };

            // $handleResponses = function (array $responses) {

            //     foreach ($responses as $response) {

            //         if ($response['state'] === Promise\PromiseInterface::FULFILLED) {

            //             // $response['value'] is an instance of \Psr\Http\Message\RequestInterface

            //             echo $response['value']->getBody();

            //         } elseif ($response['state'] === Promise\PromiseInterface::REJECTED) {

            //             // $response['reason'] is an exception

            //             echo $response['reason']->getMessage();

            //         }

            //     }

            // };

            // Promise\Utils::settle($promises())

            // ->then($handleResponses)

            // ->wait();

       

            \Log::info('response');

            \Log::info($data);

            \Log::info($messages);

            \Log::info('response');

            // $response = $client->post($url, [

            //     'headers' => [

            //         'Authorization' => 'Bearer ' . $accessToken,

            //         'Content-Type' => 'application/json',

            //     ],

            //     'json' => [

            //         'validate_only' => false,

            //         'message' => $messages,

            //     ],

            // ]);

            

            // return json_decode($response->getBody(), true);

        } catch (RequestException $e) {

            // Handle the error appropriately

            return ['error' => $e->getMessage()];

        }

    }

    protected function base64UrlEncode($data)

    {

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));

    }

}