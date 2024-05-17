<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderVendor;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class OrderVendorTaxExport implements FromCollection,WithHeadings,WithMapping{
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

        $vendor_orders = Order::with('user','paymentOption','taxes.category')->where(function ($query){
            $query->where('payment_status', 1)->whereNotIn('payment_option_id', [1,38]);
            $query->orWhere(function ($q2) {
                $q2->whereIn('payment_option_id', [1,38]);
            });
        });

        $vendor_orders = $vendor_orders->orderBy('id', 'desc');

        if (Auth::user()->is_superadmin == 0) {
            $vendor_orders = $vendor_orders->whereHas('vendors.vendor.permissionToUser', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
        }
        $vendor_orders = $vendor_orders->get();

        if(isset($this->data->date_range)){
            $date = explode(' to ',$this->data->date_range);
            $dateF = $date[0];
            $dateT = !empty($date[1]) ?$date[1]: $date[0];
            $vendor_orders = $vendor_orders->whereBetween('created_at',[$dateF." 00:00:00", $dateT." 23:59:59"]);
        }

        if(isset($this->data->payment_option)){
            $payment_option = $this->data->payment_option;
            $vendor_orders = $vendor_orders->where('payment_option_id',$payment_option);
        }

        if(isset($this->data->tax_category_option)){
            $tax_category_option = $this->data->tax_category_option;

            $vendor_orders = $vendor_orders->whereHas('taxes', function ($query) use ($tax_category_option) {
                $query->where('tax_category_id', $tax_category_option);
            });
        }

        return $vendor_orders;
    }

    public function headings(): array{
        return [
            'Order Id',
            'Date & Time',
            'Customer Name',
            'Final Amount',
            'Tax Amount',
            'Payment Method'
        ];
    }
    public function map($vendor_orders): array
    {
        return [
            $vendor_orders->order_number,
            $vendor_orders->created_date,
            $vendor_orders->user ? $vendor_orders->user->name : '',
            $vendor_orders->payable_amount,
            $vendor_orders->taxable_amount,
            $vendor_orders->paymentOption ? $vendor_orders->paymentOption->title: '',
        ];
    }
}
