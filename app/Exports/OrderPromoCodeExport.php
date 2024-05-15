<?php

namespace App\Exports;

use App\Models\OrderVendor;
use App\Models\OrderStatusOption;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class OrderPromoCodeExport implements FromCollection, WithMapping, WithHeadings{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;

    public function __construct($request)
    {
        $this->data = (object)$request->input();
    }

    public function collection()
    {
        $user = Auth::user();
        $timezone = $user->timezone ? $user->timezone : 'Asia/Kolkata';


        $vendor_orders = OrderVendor::with(['orderDetail.paymentOption', 'user','vendor','payment','orderstatus'])
            ->wherehas('orderDetail',function ($query){
                $query->where('payment_status', 1)->whereNotIn('payment_option_id', [1,38]);
                $query->orWhere(function ($q2) {
                    $q2->whereIn('payment_option_id', [1,38]);
                });
            });

        if (Auth::user()->is_superadmin == 0) {
            $vendor_orders = $vendor_orders->whereHas('vendor.permissionToUser', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
        }

        if(isset($this->data->date_range)){
            $date = explode(' to ',$this->data->date_range);
            $dateF = $date[0];
            $dateT = !empty($date[1]) ?$date[1]: $date[0];
            $dateF = Carbon::parse($dateF, $timezone)->setTimezone('UTC');
            $dateT = Carbon::parse($dateT, $timezone)->setTimezone('UTC')->addDays(1);
            $vendor_orders = $vendor_orders->whereBetween('created_at',[$dateF." 00:00:00", $dateT." 23:59:59"]);
        }

        if(isset($this->data->order_status)){
            $status = $this->data->order_status;
            if($this->data->order_status =='Placed'){
                $status = '1';
            }elseif($this->data->order_status =='Accepted'){
                $status = '2';
            }elseif($this->data->order_status =='Out For Delivery'){
                $status = '5';
            }elseif($this->data->order_status =='Rejected'){
                $status = '3';
            }elseif($this->data->order_status =='Processing'){
                $status = '4';
            }elseif($this->data->order_status =='Delivered'){
                $status = '6';
            }
            $vendor_orders = $vendor_orders->where('order_status_option_id',$status);
        }

        if(isset($this->data->promo_code_option)){

            $promo_code_option = $this->data->promo_code_option;
            $vendor_orders = $vendor_orders->where('coupon_id',$promo_code_option);
        }


        $vendor_orders = $vendor_orders->get();
        foreach ($vendor_orders as $vendor_order) {
            if($vendor_order->coupon_paid_by == 0){
                $vendor_order->vendor_paid_promo = $vendor_order->discount_amount ?  $vendor_order->discount_amount : '0.00';
                $vendor_order->admin_paid_promo = '0.00';
            }else{
                $vendor_order->admin_paid_promo = $vendor_order->discount_amount ?  $vendor_order->discount_amount : '0.00';
                $vendor_order->vendor_paid_promo = '0.00';
            }

            if($vendor_order->orderstatus){
                $order_status_detail = $vendor_order->orderstatus->where('order_id', $vendor_order->order_id)->orderBy('id', 'DESC')->first();
                if($order_status_detail){
                    $order_status_option = OrderStatusOption::where('id', $order_status_detail->order_status_option_id)->first();
                    if($order_status_option){
                        $order_status = $order_status_option->title;
                    }
                }
            }
            $vendor_order->order_status = $order_status;
        }
        return $vendor_orders;
    }
    public function headings(): array{
        return [
            'Order Id',
            'Date & Time',
            'Customer Name',
            'Vendor Name',
            'Subtotal Amount',
            'Promo Code Discount [Vendor Paid Promos]',
            'Promo Code Discount [Admin Paid Promos]',
            'Final Amount',
            'Payment Method',
            'Order Status',
        ];
    }

    public function map($order): array
    {
        return [
            $order->orderDetail->order_number,
            $order->created_at,
            $order->user ? $order->user->name : '',
            $order->vendor->name,
            $order->subtotal_amount,
            $order->admin_paid_promo,
            $order->vendor_paid_promo,
            $order->payable_amount,
            $order->payment_method,
            $order->order_status,
        ];
    }

}
