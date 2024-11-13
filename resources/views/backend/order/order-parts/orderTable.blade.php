    @if(count($orders['orders']) > 0)
    @foreach ($orders['orders'] as $k => $order)

        <div class="{{$ClassName}} al_order_sec" id="full-order-div{{ $k }}">
            <div class="row no-gutters order_head mb-2">
                <div class="col-md-3 alOrderStatus">
                    <h4>{{ __('Order ID') }}</h4>
                    <span>#{{ @$order['order_number'] }} </span>

                    @if ($order['vendors'][0]['exchanged_of_order'] && $order['vendors'][0]['exchanged_of_order']['order_detail'])
                        <h4>{{ __('Exchange of Order') }}</h4>
                        <a href="{{ $order['vendors'][0]['exchanged_of_order']['vendor_detail_url'] }}">
                            <span>#{{ order['vendors'][0]['exchanged_of_order']['order_detail']['order_number'] }}</span></a>
                    @endif
                    @if ($order['vendors'][0]['exchanged_to_order'] && $order['vendors'][0]['exchanged_to_order']['order_detail'])
                        <h4>{{ __('Exchanged to Order') }}</h4>
                        <a href="{{ $order['vendors'][0]['exchanged_to_order']['vendor_detail_url'] }}">
                            <span>#{{ $order['vendors'][0]['exchanged_to_order']['order_detail']['order_number'] }}></span></a>
                    @endif
                </div>
                <div class="col-md-3 alOrderStatus">
                    <h4>{{ __('Date & Time') }}</h4>
                    <span>{{ $order['created_date'] }}</span>
                </div>
                <div class="col-md-3 alOrderStatus">
                    <h4>{{ __('Customer') }}</h4>
                    <span>{{ @$order['user']['name']??'' }}</span>
                </div>
                @if (Auth::user()->is_superadmin || $client_preference_detail->hide_order_address == 0)
                    <div class="col-md-3">
                        <h4>{{ __('Address') }}</h4>
                        @if ($order['address'] !== null)
                            <div class="col-md-12 pl-0 ellips">
                                <span class="mb-0 " data-toggle="tooltip" data-placement="top"
                                    title="{{ $order['address']['address'] }}">
                                    {{ $order['address']['house_number'] ? $order['address']['house_number'] . ',' : '' }}
                                    {{ $order['address']['address'] }}
                                    </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="row mb-3">
                <div class="col-md-9">

                    @foreach ($order['vendors'] as $ve => $vendor)
                        <div class="row  {{ $ve == 0 ? 'mt-0' : 'mt-2' }}"
                            id="single-order-div{{ $k }}{{ $ve }}">
                            <div class="col-12 order-hover-btn">



                                <div
                                    class="order_detail order_detail_data align-items-top pb-1 mb-0 card-box no-gutters h-100">
                                    <ul class="alBtnsOnOrders d-flex justify-content-end">
                                        @if (!Auth::user()->is_superadmin && @$clientData->socket_url)
                                            <li>
                                                <a data-toggle="tooltip" data-placement="top" title="Start Chat"
                                                    class="start_chat btn-info" data-vendor_order_id="{{ $vendor->id }}"
                                                    data-vendor_id="{{ $vendor['vendor_id'] }}"
                                                    data-orderId="{{ $order['order_id'] }}"
                                                    data-order_id="{{ $order['id'] }}"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-chat-dots-fill"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                                    </svg></a>
                                            </li>
                                        @endif
                                    </ul>
                                    
                                    <a href="{{ $vendor['vendor_detail_url'] }}" class="row">
                                        @if ($order['scheduled_date_time'] || $order['luxury_option_name'] != '')
                                            <div class="col-sm-12">
                                                <div
                                                    class="progress-order font-12  d-flex align-items-center justify-content-between pr-2">
                                                    @if ($order->luxury_option_name != '')
                                                        <span
                                                            class="badge badge-info ml-2 my-1 badge_{{ $order->luxury_option_id }}">{{ $order->luxury_option_name }}</span>
                                                    @endif
                                                    @if ($vendor->order_status == 'Accepted' && $vendor->accepted_by != null)
                                                        <span class="ml-2 text-info">{{ $vendor->order_status }} by
                                                            {{ @$vendor->accepted_by->name }}</span>
                                                    @endif
                                                    @if ($order->is_gift == '1')
                                                        <div class="gifted-icon">
                                                            <img class="p-1 align-middle"
                                                                src="{{ asset('assets/images/gifts_icon.png') }}"
                                                                alt="">
                                                            <span class="align-middle">This is a gift.</span>
                                                        </div>
                                                    @endif
                                                    @if ($order->scheduled_date_time || $order->schedule_pickup || $order->schedule_dropoff)
                                                        <span class="badge badge-success ml-2">{{ __('Scheduled') }}</span>
                                                    @endif


                                                    @if ($vendor->delivery_fee > 0 || $order->scheduled_date_time || $order->schedule_pickup)
                                                        @if ($vendor->order_status != 'Rejected')
                                                            @if ($client_preferences->scheduling_with_slots == 1 && $client_preferences->business_type == 'laundry')
                                                                <span class="ml-2 text-right">Slots: Pickup:
                                                                    {{ $order->scheduled_slot }} | Dropoff:
                                                                    {{ $order->dropoff_scheduled_slot }}
                                                                </span>
                                                            @else
                                                                @if ($order->scheduled_slot == null)
                                                                    @if ($order->scheduled_date_time)
                                                                        <span
                                                                            class="ml-2">{{ __('Order scheduled for') }}
                                                                            {{ $order->scheduled_date_time }}</span>
                                                                    @else
                                                                        <span
                                                                            class="ml-2">{{ __('Expected Delivery by') }}
                                                                            {{ $vendor->ETA }}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="ml-2">{{ __('Order scheduled for') }}
                                                                        {{ $order->order_schedule_date }},
                                                                        {{ __('Slot') }} :
                                                                        {{ $order->scheduled_slot }}</span>
                                                                @endif
                                                            @endif
                                                        @elseif($vendor->order_status == 'Rejected' && $vendor->cancelled_by != null)
                                                            <span class="ml-2 text-danger">{{ $vendor->order_status }} by
                                                                {{ @$vendor->cancelled_by->name }}</span>
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>
                                        @endif
                                        <span class="left_arrow pulse">
                                        </span>
                                        <div class="col-5 col-sm-3">
                                            <h5 class="m-0">{{ $vendor['vendor_name'] }}</h5>
                                            <ul class="status_box mt-1 pl-0">
                                                <li>
                                                    <img src="{{ asset('assets/images/order-icon.svg') }}" alt="">
                                                    <label class="m-0 in-progress">{{ $vendor['order_status'] }}</label>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-6 col-sm-5">
                                            <div class="row no-gutters product_list align-items-center flex-wrap">

                                                @foreach ($vendor['products'] as $pr => $product)
                                                    <div class="col-4 text-center mb-2">
                                                        <div class="list-img" style="height:50px;">
                                                            <img style="height:50px;" data-placement="right"
                                                                data-toggle="tooltip"
                                                                title="{{ $product['product_name'] }}>"
                                                                src="{{ $product['image_path']['proxy_url'] . '74/100' . $product['image_path']['image_path'] }}">
                                                            <span
                                                                class="item_no position-absolute">x{{ $product['quantity'] }}</span>

                                                        </div>

                                                        <label class="items_price">
                                                            ({{ $product['product_title'] }})
                                                            {{ $clientCurrency->currency->symbol }}{{ decimal_format($product['price']) }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-md-0 mt-sm-2">
                                            <ul class="price_box_bottom m-0 p-0">

                                                @if ($vendor['subtotal_amount'] > 0 || $vendor['subtotal_amount'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Total') }}</label>
                                                        <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_amount + $order->bid_discount) }}</span>
                                                        <!-- <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->payable_amount) }}</span>
                                                        <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['subtotal_amount'] + $vendor['bid_discount']) }}</span> -->
                                                    </li>
                                                @endif

                                                @if ($vendor['bid_discount'] > 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Bid Discount') }}</label>
                                                        <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['bid_discount']) }}</span>
                                                    </li>
                                                @endif

                                                @if ($vendor['additional_price'] > 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Additional Price') }}</label>
                                                        <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['additional_price']) }}</span>

                                                    </li>
                                                @endif

                                                @if ($vendor['discount_amount'] > 0 || $vendor['discount_amount'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Promocode') }}</label>
                                                        <span>-{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['discount_amount']) }}
                                                        </span>
                                                    </li>
                                                @endif

                                                @if ($vendor['total_container_charges'] > 0 || $vendor['total_container_charges'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Container Charges') }}</label>
                                                        @if ($vendor['total_container_charges'] !== null)
                                                            <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['total_container_charges']) }}</span>
                                                        @else
                                                            <span>{{ $clientCurrency->currency->symbol }}0.00</span>
                                                        @endif
                                                    </li>
                                                @endif

                                                @if ($vendor['taxable_amount'] > 0 || $vendor['taxable_amount'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Tax') }}</label>
                                                        @if ($vendor['taxable_amount'] !== null)
                                                            <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['taxable_amount']) }}</span>
                                                        @else
                                                            <span>{{ $clientCurrency->currency->symbol }}0.00</span>
                                                        @endif
                                                    </li>
                                                @endif
                                                @if ($vendor['toll_amount'] > 0 || $vendor['toll_amount'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Toll Fee') }}</label>
                                                        @if ($vendor['toll_amount'] !== null)
                                                            <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['toll_amount']) }}</span>
                                                        @else
                                                            <span>{{ $clientCurrency->currency->symbol }} 0.00</span>
                                                        @endif
                                                    </li>
                                                @endif

                                                @if ($vendor['service_fee_percentage_amount'] > 0 || $vendor['service_fee_percentage_amount'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Service Fee') }}</label>
                                                        @if ($vendor['service_fee_percentage_amount'] !== null)
                                                            <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['service_fee_percentage_amount']) }}</span>
                                                        @else
                                                            <span>{{ $clientCurrency->currency->symbol }}0.00</span>
                                                        @endif
                                                    </li>
                                                @endif

                                                @if ($vendor['fixed_fee'] > 0 || $vendor['fixed_fee'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __($fixedFee) }}</label>
                                                        @if ($vendor['fixed_fee'] !== null)
                                                            <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['fixed_fee']) }}</span>
                                                        @else
                                                            <span>{{ $clientCurrency->currency->symbol }}0.00</span>
                                                        @endif
                                                    </li>
                                                @endif


                                                @if ($vendor['delivery_fee'] > 0 || $vendor['delivery_fee'] < 0)
                                                    <li class="d-flex align-items-center justify-content-between">
                                                        <label class="m-0">{{ __('Delivery') }}</label>
                                                        @if ($vendor['delivery_fee'] !== null)
                                                            <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($vendor['delivery_fee']) }}</span>
                                                        @else
                                                            <span>{{ $clientCurrency->currency->symbol }}0.00</span>
                                                        @endif
                                                    </li>
                                                @endif

                                                <li class="grand_total d-flex align-items-center justify-content-between">
                                                    <label class="m-0">{{ __('Amount') }}</label>
                                                    @if ($vendor['delivery_fee'] == '' || $vendor['delivery_fee'] == null)
                                                        <!-- {{ $vendor['delivery_fee'] = 0 }} -->
                                                    @endif
                                                     <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->payable_amount) }}</span>

                                                </li>
                                                    <!-- <li>{{print_r($vendor);}}</li> -->
                                                    <!-- <li> {{'subtotal_amount:'. $vendor['subtotal_amount']}}</li>
                                                    <li> {{'discount_amount:'. $vendor['discount_amount']}}</li>
                                                    <li> {{'total_container_charges:'. $vendor['total_container_charges']}}</li>
                                                    <li> {{'taxable_amount:'. $vendor['taxable_amount']}}</li>
                                                    <li> {{'service_fee_percentage_amount:'. $vendor['service_fee_percentage_amount']}}</li>
                                                    <li> {{'fixed_fee:'. $vendor['fixed_fee']}}</li>
                                                    <li> {{'delivery_fee:'. $vendor['delivery_fee']}}</li>
                                                    <li> {{'additional_price:'. $vendor['additional_price']}}</li>
                                                    <li> {{'toll_amount:'. $vendor['toll_amount']}}</li>
                                                    <li> {{'$order->wallet_amount_used:'. $order->wallet_amount_used}}</li> -->


                                            </ul>
                                        </div>



                                    </a>
                                    <div id="update-single-status" class="my-2">



                                        @if ($vendor['order_status_option_id'] == 1)
                                            @if ($order->vendors->first()->exchanged_of_order)
                                                <button class="update-status-ar btn-info"
                                                    data-full_div="#full-order-div{{ $k }}"
                                                    data-single_div="#single-order-div{{ $k . $ve }}"
                                                    data-count="{{ $ve }}" data-order_id="{{ $order->id }}"
                                                    data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="2"
                                                    data-order_vendor_id="{{ $vendor->id }}"
                                                    data-is_alert="{{ $vendor->isAlert }}"
                                                    data-alert_message="{{ $vendor->alertMessage }}">{{ __('Exchange Accept') }}</button>
                                            @else
                                                <button class="update-status-ar btn-info"
                                                    data-full_div="#full-order-div{{ $k }}"
                                                    data-single_div="#single-order-div{{ $k . $ve }}"
                                                    data-count="{{ $ve }}"
                                                    data-order_id="{{ $order->id }}"
                                                    data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="2"
                                                    data-order_vendor_id="{{ $vendor->id }}"
                                                    data-is_alert="{{ $vendor->isAlert }}"
                                                    data-alert_message="{{ $vendor->alertMessage }}">{{ __('Accept') }}</button>
                                            @endif
                                        @elseif($vendor->order_status_option_id == 2)
                                            <button class="update-status-ar btn-warning"
                                                data-full_div="#full-order-div{{ $k }}"
                                                data-single_div="#single-order-div{{ $k . $ve }}"
                                                data-count="{{ $ve }}" data-order_id="{{ $order->id }}"
                                                data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="4"
                                                data-order_vendor_id="{{ $vendor->id }}"
                                                data-order_luxury_option="{{ $order->luxury_option_id }}">{{ __('Processing') }}</button>
                                        @elseif($vendor->order_status_option_id == 4)
                                            <button class="update-status-ar btn-success"
                                                data-full_div="#full-order-div{{ $k }}"
                                                data-single_div="#single-order-div{{ $k . $ve }}"
                                                data-count="{{ $ve }}" data-order_id="{{ $order->id }}"
                                                data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="5"
                                                data-order_vendor_id="{{ $vendor->id }}">
                                                @if ($order->luxury_option_id == 2 || $order->luxury_option_id == 3)
                                                    {{ __('Order Prepared') }}
                                                @else
                                                    {{ __('Out For Delivery') }}
                                                @endif
                                            </button>
                                        @elseif($vendor->order_status_option_id == 5)
                                            <button class="update-status-ar btn-info"
                                                data-full_div="#full-order-div{{ $k }}>"
                                                data-single_div="#single-order-div{{ $k . $ve }}"
                                                data-count="{{ $ve }}" data-order_id="{{ $order->id }}"
                                                data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="6"
                                                data-order_vendor_id="{{ $vendor->id }}">{{ __('Delivered') }}</button>
                                        @else
                                        @endif
                                        @if (
                                            $vendor->order_status_option_id == 1 ||
                                                ($vendor->order_status_option_id != 6 &&
                                                    $vendor->order_status_option_id != 3 &&
                                                    $vendor->order_status_option_id != 9))
                                            @if ($order->vendors->first()->exchanged_of_order)
                                                <button class="update-status-ar btn-danger" id="reject"
                                                    data-full_div="#full-order-div{{ $k }}"
                                                    data-single_div="#single-order-div{{ $k . $ve }}"
                                                    data-count="{{ $ve }}"
                                                    data-order_id="{{ $order->id }}"
                                                    data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="3"
                                                    data-order_vendor_id="{{ $vendor->id }}">{{ __('Exchange Reject') }}</button>
                                            @else
                                                <button class="update-status-ar btn-danger" id="reject"
                                                    data-full_div="#full-order-div{{ $k }}"
                                                    data-single_div="#single-order-div{{ $k }}{{ $ve }}"
                                                    data-count="{{ $ve }}"
                                                    data-order_id="{{ $order->id }}"
                                                    data-vendor_id="{{ $vendor->vendor_id }}" data-status_option_id="3"
                                                    data-order_vendor_id="{{ $vendor->id }}">{{ __('Reject') }}</button>
                                            @endif
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-3 pl-0">
                    <div class="card-box p-2 mb-0 w-100 h-100">
                        <ul class="price_box_bottom m-0 pl-0 pt-1">
                            <li class="d-flex align-items-center justify-content-between">
                                <label class="m-0">{{ __('Total') }}</label>
                                <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_amount + $order->bid_discount) }}</span>
                            </li>

                            @if ($order->bid_discount > 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Bid Discount') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->bid_discount) }}</span>
                                </li>
                            @endif

                            @if ($order->additional_price > 0 || $order->additional_price < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Tax') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->additional_price) }}</span>
                                </li>
                            @endif

                            @if ($order->total_other_taxes_amount > 0 || $order->total_other_taxes_amount < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Tax') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_other_taxes_amount) }}</span>
                                </li>
                            @endif

                       {{--     @if ($order->taxable_amount > 0 || $order->taxable_amount < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Tax') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->taxable_amount) }}</span>
                                </li>
                            @endif--}}

                            {{-- need to check --}}

                            @if ($order->total_toll_amount > 0 || $order->total_toll_amount < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Toll Fee') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_toll_amount) }}</span>
                                </li>
                            @endif

                            @if ($order->total_service_fee > 0 || $order->total_service_fee < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Service Fee') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_service_fee) }}</span>
                                </li>
                            @endif


                            @if ($order->fixed_fee_amount > 0 || $order->fixed_fee_amount < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __($fixedFee) }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->fixed_fee_amount) }}</span>
                                </li>
                            @endif

                            @if ($order->total_delivery_fee > 0 || $order->total_delivery_fee < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Delivery Fee') }}</label><span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_delivery_fee) }}</span>
                                </li>
                            @endif

                            @if ($order->giftCardUsed == 1)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('gift card') }}</label>
                                    <span>-{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->gift_card_amount) }}</span>
                                </li>
                            @endif

                            @if ($order->total_container_charges > 0 || $order->total_container_charges < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Total Container Charges') }}</label>
                                    @if ($order->total_container_charges !== null)
                                        <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->total_container_charges) }}</span>
                                    @else
                                        <span>{{ $clientCurrency->currency->symbol }}0.00</span>
                                    @endif
                                </li>
                            @endif

                            @if ($order->tip_amount > 0 || $order->tip_amount < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Tip Amount') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->tip_amount) }}</span>
                                </li>
                            @endif

                            @if ($order->loyalty_amount_saved > 0 || $order->loyalty_amount_saved < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Loyalty Used') }}</label>
                                    <span>-{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->loyalty_amount_saved) }}</span>
                                </li>
                            @endif


                            @if ($order->wallet_amount_used > 0 || $order->wallet_amount_used < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Wallet Amount Used') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->wallet_amount_used) }}</span>
                                </li>
                            @endif


                            @if ($order->total_discount_calculate > 0 || $order->total_discount_calculate < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Total Discount') }}</label>
                                    <span>{{$order->total_discount_calculate == 0 ?"":"-"}}{{ $clientCurrency->currency->symbol }}{{ decimal_format( $order->total_discount_calculate) }}</span>
                                </li>
                            @endif

                            @if ($order->rental_protection_amount > 0 || $order->rental_protection_amount < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Rental Protection Amount') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->rental_protection_amount) }}</span>
                                </li>
                            @endif

                            @if ($order->booking_option_price > 0 || $order->booking_option_price < 0)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Booking Option Amount') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->booking_option_price) }}</span>
                                </li>
                            @endif
                            @if($order->luxury_option_id == 4)
                                <li class="d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Security Amount') }} </label>
                                    <span>{{Session::get('currencySymbol').decimal_format($order->security_amount)}}</span>
                                </li>
                            @endif
                            <li class="grand_total d-flex align-items-center justify-content-between">
                                <label class="m-0">{{ __('Payable') }} </label>
                                <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->payable_amount) }}</span>
                            </li>

                            {{-- mohit sir branch code added by sohail --}}

                            @if ($order->advance_amount > 0 || $order->advance_amount == 0)
                                <li class="grand_total d-flex align-items-center justify-content-between">
                                    <label class="m-0">{{ __('Advance Paid') }} </label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->advance_amount) }}</span>
                                </li>
                                <li class="grand_total d-flex align-items-center justify-content-between">
                                    <label class="m-0"> {{$filter_order_status == 'orders_history' ? __('Payed Amount') : __('Pending Amount') }}</label>
                                    <span>{{ $clientCurrency->currency->symbol }}{{ decimal_format($order->payable_amount - $order->advance_amount) }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    @endforeach
@else
<div class="error-msg mt-3">
    <img class="mb-2" src="{{asset('images/no-order.svg')}}">
    <p>{{ __("You don't have orders right now.") }}</p>
</div>
@endif

