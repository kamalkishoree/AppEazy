<?php

namespace App\Listeners;

use App\Events\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use Carbon\Carbon;
use App\Model\Roster;
use App\Model\Client;
use Config;
use Illuminate\Support\Facades\DB;
use Exception;
use Kawankoding\Fcm\Fcm;
use App\Services\FirebaseService;

class SendPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /**
     * Handle the event.
     *
     * @param  PushNotification  $event
     * @return void
     */
    public function handle(PushNotification $event)
    {

        $date =  Carbon::now()->toDateTimeString();
        try {
            $schemaName = 'royodelivery_db';
            $default = [
                'driver' => env('DB_CONNECTION', 'mysql'),
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => $schemaName,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null
            ];
            Config::set("database.connections.$schemaName", $default);
            config(["database.connections.mysql.database" => $schemaName]);
            $this->getData();
            DB::disconnect($schemaName);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function getData()
    {
        $schemaName       = 'royodelivery_db';
        $date             =  Carbon::now()->toDateTimeString();

        $get              =  DB::connection($schemaName)->table('rosters')
            ->where(function ($query) use ($date) {
                $query->where('notification_time', '<=', $date)
                    ->orWhere('notification_befor_time', '<=', $date);
            })->where('status', 0)
            ->leftJoin('roster_details', 'rosters.detail_id', '=', 'roster_details.unique_id')
            ->select(
                'rosters.*',
                'roster_details.customer_name',
                'roster_details.customer_phone_number',
                'roster_details.short_name',
                'roster_details.address',
                'roster_details.lat',
                'roster_details.long',
                'roster_details.task_count'
            );
        $getids           = $get->pluck('id')->toArray();
        $get              = $get->get();
        DB::connection($schemaName)->table('rosters')->where('status', 10)->delete();

        if (count($getids) > 0) {
            // DB::connection($schemaName)->table('rosters')->whereIn('id',$getids)->update(['status'=>1]);
            DB::connection($schemaName)->table('rosters')->whereIn('id', $getids)->delete();
            $this->sendnotification($get);
        } else {
            // $this->extraTime($schemaName);
        }
        return;
    }

    public function sendnotification($recipients)
    {

        Log::info('notification recipients ');
        Log::info([$recipients]);
        try {
            $array = json_decode(json_encode($recipients), true);
            $counter = 1;
            foreach ($array as $item) {


                if (isset($item['device_token']) && !empty($item['device_token'])) {
                    $item['title']     = 'Pickup Request';
                    $item['body']      = 'Check All Details For This Request In App';
                    $new = [];
                    $item['notificationType'] = $item['type'];
                    unset($item['type']); // done by Preet due to notification title is displaying like AR in iOS



                    array_push($new, $item['device_token']);
                    $clientRecord = Client::where('code', $item['client_code'])->first();

                    Log::info(['$item' => $item]);

                    $this->seperate_connection('db_' . $clientRecord->database_name);
                    $client_preferences = DB::connection('db_' . $clientRecord->database_name)->table('client_preferences')->where('client_id', $item['client_code'])->first();

                    if (isset($new)) {
                        try {
                            // $fcm_server_key = !empty($client_preferences->fcm_server_key)? $client_preferences->fcm_server_key : 'null';
                            // $fcmObj = new Fcm($fcm_server_key);
                            if (($item['is_particular_driver'] ?? 0) != 2) {
                                $data = [
                                    // "registration_ids" => is_array($item['device_token']) ? $item['device_token'] : array($item['device_token']),//$item['device_token'],
                                    "token" => $item['device_token'],
                                    "notification" => [
                                        'title' => 'Pickup Request',
                                        'body' => 'Check All Details For This Request In App',
                                        'sound' => 'notification.mp3',
                                        "android_channel_id" => "Royo-Delivery",
                                    ],
                                    // "data" => json_encode($item),
                                    "priority" => "high"
                                ];
                                $response = FirebaseService::sendSingleNotification($data, $item);
                                $counter++;
                                // $fcm_store = $fcmObj->to([$item['device_token']]) // $recipients must an array
                                //         ->priority('high')
                                //         ->timeToLive(0)
                                //         ->data($item)
                                //         ->notification([
                                //             'title'              => 'Pickup Request',
                                //             'body'               => 'Check All Details For This Request In App',
                                //             'sound'              => 'notification.mp3',
                                //             'android_channel_id' => 'Royo-Delivery',
                                //             'soundPlay'          => true,
                                //             'show_in_foreground' => true,
                                //         ])
                                // ->send();
                                //\Log::info( "fcm" );
                                //\Log::info( $fcm_store );
                            } else {
                                $data = [
                                    "registration_ids" => is_array($item['device_token']) ? $item['device_token'] : array($item['device_token']), //$item['device_token'],
                                    "notification" => [
                                        'title' => 'Reminder Order',
                                        'body' => 'Pickup your order #' . $item['order_id'],
                                    ],
                                    "data" => [
                                        'title' => 'Reminder Order',
                                        'body' => 'Pickup your order #' . $item['order_id'],
                                    ],
                                    "priority" => "high"
                                ];
                                $response = FirebaseService::sendSingleNotification($data, $item);
                                Log::info($response, ['location' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0]]);
                                // $fcm_store =   $fcmObj
                                // ->to([$item['device_token']])
                                // ->priority('high')
                                // ->timeToLive(0)
                                // ->data([
                                //     'title' => 'Reminder Order',
                                //     'body' => 'Pickup your order #'.$item['order_id'],
                                // ])
                                // ->notification([
                                //     'title' => 'Reminder Order',
                                //     'body' => 'Pickup your order #'.$item['order_id'],
                                // ])
                                // ->send();
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                        }
                    }
                }
            }
            sleep(5);
            // $this->getData();
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }

    public function extraTime($schemaName)
    {
        //sleep(30); ->addSeconds(45)
        $date =  Carbon::now()->toDateTimeString();
        $check = DB::connection($schemaName)->table('rosters')->where(function ($query) use ($date) {
            $query->where('notification_time', '<=', $date)
                ->orWhere('notification_befor_time', '<=', $date);
        })->get();
        if (count($check) > 0) {
            sleep(15);
            $this->getData();
        } else {
            return;
        }
    }

    public function seperate_connection($schemaName)
    {
        $default = [
            'driver' => env('DB_CONNECTION', 'mysql'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $schemaName,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null
        ];
        Config::set("database.connections.$schemaName", $default);
    }
}
