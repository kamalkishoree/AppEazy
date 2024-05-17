<?php

namespace App\Exports;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrderLoyaltyExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */


    public $data;

    public function __construct($request)
    {
        $this->data = (object)$request->input();
    }

    public function collection(){
        $user = Auth::user();
        $timezone = $user->timezone ? $user->timezone : 'Asia/Kolkata';

        $orders = Order::with('user','paymentOption','loyaltyCard')->where(function ($query){
            $query->where('payment_status', 1)->whereNotIn('payment_option_id', [1,38]);
            $query->orWhere(function ($q2) {
                $q2->whereIn('payment_option_id', [1,38]);
            });
        });

        $orders = $orders->orderBy('id', 'desc');

        if (Auth::user()->is_superadmin == 0) {
            $orders = $orders->whereHas('vendors.vendor.permissionToUser', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
        }


        if(isset($this->data->date_range)){
            $date = explode(' to ',$this->data->date_range);
            $dateF = $date[0];
            $dateT = !empty($date[1]) ?$date[1]: $date[0];
            $dateF = Carbon::parse($dateF, $timezone)->setTimezone('UTC');
            $dateT = Carbon::parse($dateT, $timezone)->setTimezone('UTC')->addDays(1);
            $orders = $orders->whereBetween('created_at',[$dateF." 00:00:00", $dateT." 23:59:59"]);
        }


        if(isset($this->data->loyalty_membership)){
            $option = $this->data->loyalty_membership;
            if($this->data->loyalty_membership =='Bronze'){
                $option = '1';
            }elseif($this->data->loyalty_membership =='Silver'){
                $option = '2';
            }elseif($this->data->loyalty_membership =='Gold'){
                $option = '3';
            }elseif($this->data->loyalty_membership =='Platinum'){
                $option = '5';
            }
            $orders = $orders->where('loyalty_membership_id',$option);
        }


        if(isset($this->data->payment_option)){
            $payment_option = $this->data->payment_option;
            $orders = $orders->where('payment_option_id',$payment_option);
        }

        $orders = $orders->get();

        foreach ($orders as $order) {
            $order->loyalty_membership = $order->loyaltyCard ? $order->loyaltyCard->name : '';
            $order->loyalty_points_used = $order->loyalty_points_used ? $order->loyalty_points_used : '0.00';
            $order->created_date = dateTimeInUserTimeZone($order->created_at, $timezone);
            $order->loyalty_points_earned = $order->loyalty_points_earned ? $order->loyalty_points_earned : '0.00';
        }
        return $orders;
    }
    public function headings(): array{
        return [
            'Order Id',
            'Date & Time',
            'Customer Name',
            'Final Amount',
            'Loyalty Used',
            'Loyality Membership',
            'Loyality Earned',
            'Payment Method',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_date,
            $order->user ? $order->user->name : '',
            $order->payable_amount,
            $order->loyalty_points_used,
            $order->loyalty_membership,
            $order->loyalty_points_earned,
            $order->paymentOption ? $order->paymentOption->title: '',
        ];
    }
}
