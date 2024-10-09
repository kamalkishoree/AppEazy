<div class="tab-pane fade {{ Request::query('pageType') === null || Request::query('pageType') == 'activeOrders' ? 'active show' : '' }}"
                                                id="active-orders" role="tabpanel" aria-labelledby="active-orders-tab">
                                                <div class="row">
                                                    @if ($activeOrders->isNotEmpty())
                                                        @foreach ($activeOrders as $key => $order)
                                                        @php

                                                            $total_other_taxes=0.00;
                                                         	if(!empty($order->total_other_taxes)){
                                                                $total_other_taxes  =   (float) array_sum(explode(":", $order->total_other_taxes));
                                                            }else{
                                                                $total_other_taxes = $order->taxable_amount;
                                                            }

                                                        @endphp
                                                            <div class="col-12">
                                                                <div class="row no-gutters order_head">
                                                                    <div class="col-md-3 alOrderStatus">
                                                                        <h4>{{ __('Order Number') }}</h4>
                                                                        <span>#{{ $order->order_number }}</span>

                                                                        <?php  $is_exchanged_order = 0;  ?>
                                                                    @if(@$order->vendors[0]->exchanged_of_order)
                                                                    <?php  $is_exchanged_order = 1;  ?>
                                                                        <h4>{{ __('Exchanged Order Number') }}</h4>
                                                                        <span>#{{ $order->vendors[0]->exchanged_of_order->orderDetail->order_number }}</span>


                                                                    @endif
                                                                    </div>
                                                                    <div class="col-md-3 alOrderStatus">
                                                                        <h4>{{ __('Date & Time') }}</h4>
                                                                        <span>{{ dateTimeInUserTimeZone($order->created_at, $timezone) }}</span>
                                                                    </div>
                                                                    <div class="col-md-3 alOrderStatus">
                                                                        <h4>{{  __('Customer Name') }}</h4>
                                                                        <span><a class="text-capitalize">{{ $order->user->name }}</a></span>
                                                                    </div>
                                                                    @if ($client_preference_detail->business_type != 'taxi')
                                                                        <div class="col-md-3 ellipsis">
                                                                            <h4>{{ __('Address') }}</h4>
                                                                            <div class="alOrderAddressBox">
                                                                                @include('frontend.account.orders.order_address')
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="row no-gutters order_data d-none">
                                                                    <div class="col-md-3">#{{ $order->order_number }}
                                                                    </div>
                                                                    {{-- <div class="col-md-3">{{convertDateTimeInTimeZone($order->created_at, $timezone, 'l, F d, Y, h:i A')}}</div> --}}
                                                                    <div class="col-md-3">
                                                                        {{ dateTimeInUserTimeZone($order->created_at, $timezone) }}
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <a class="text-capitalize">{{ $order->user->name }}</a>
                                                                    </div>
                                                                    @if ($client_preference_detail->business_type != 'taxi')
                                                                        <div class="col-md-3">
                                                                            <h4>{{ __('Address') }}</h4>
                                                                            <div class="alOrderAddressBox">
                                                                                @include('frontend.account.orders.order_address')
                                                                            </div>

                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="row mt-2">
                                                                    <div class="col-md-9 mb-3">
                                                                        @php
                                                                            $subtotal_order_price = $total_order_price = $total_tax_order_price = 0;
                                                                        @endphp
                                                                        @foreach ($order->vendors as $key => $vendor)
                                                                            @php
                                                                                $product_total_count = $product_subtotal_amount = $product_taxable_amount = 0;
                                                                            @endphp
                                                                            <div class="order_detail order_detail_data align-items-top pb-3 card-box no-gutters mb-0">
                                                                                @if ($vendor->delivery_fee > 0 || !empty($order->scheduled_date_time) || $order->luxury_option_id > 0)
                                                                                    <div class="progress-order font-12  d-flex align-items-center justify-content-between pr-2">
                                                                                        @if ($order->luxury_option_id > 0)
                                                                                            @php
                                                                                                $luxury_option = \App\Models\LuxuryOption::where('id', $order->luxury_option_id)->first();
                                                                                                if ($luxury_option->title == 'takeaway') {
                                                                                                    $luxury_option_name = getNomenclatureName('Takeaway', Session::get('customerLanguage'), false);
                                                                                                } elseif ($luxury_option->title == 'dine_in') {
                                                                                                    $luxury_option_name = 'Dine-In';
                                                                                                } else {
                                                                                                  //  $luxury_option_name = 'Delivery';
                                                                                                    $luxury_option_name = getNomenclatureName($luxury_option->title);

                                                                                                }
                                                                                            @endphp
                                                                                            <span>
                                                                                                @if(!empty($order->scheduled_date_time) && $clientPreference->is_order_edit_enable == 1 && $clientPreference->order_edit_before_hours > 0 && $order->luxury_option_id != 4 && ($order->payment_option_id==1 || $order->payment_status !=1))
                                                                                                    @if((strtotime($order->scheduled_date_time) - strtotime($clientPreference->editlimit_datetime)) > 0)
                                                                                                        @if(!empty($order->editingInCart))
                                                                                                            <span class="badge ml-2" style="font-size:12px;">
                                                                                                            {{ __("This Order is being edited") }} <a class="discard_editing_order" style="color:var(--theme-deafult);" href="javascript:void(0)" data-orderid="{{$order->id}}"><i class="fa fa-trash-o"></i> {{__('Discard')}}</a>
                                                                                                            </span>
                                                                                                        @else
                                                                                                            @if($vendor->order_status_option_id <= 2)
                                                                                                                <span class="badge ml-2" style="cursor:pointer;font-size:14px;">
                                                                                                                    <strong><a class="order_edit_button" data-order_id='{{$order->id}}'><i class="fa fa-pencil-square-o" aria-hidden="true"></i> {{__('Edit')}}</a></strong>
                                                                                                                </span>
                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                            <span class="badge badge-info ml-2 my-1">{{ __($luxury_option_name) }} </span>
                                                                                            </span>
                                                                                        @endif
                                                                                        @if (!empty($order->scheduled_date_time))
                                                                                            <span
                                                                                                class="badge badge-success ml-2">{{__('Scheduled')}}</span>
                                                                                                <span class="ml-2 text-right">
                                                                                                    Slots:
                                                                                                    @if($clientPreference->scheduling_with_slots == 1 && $clientPreference->business_type == 'laundry')
                                                                                                        {{'Pickup: '. date('Y-m-d', strtotime(dateTimeInUserTimeZone($order->schedule_pickup, $timezone))).' '.$order->scheduled_slot.' | ' }}

                                                                                                        @if ($order->dropoff_scheduled_slot != "")
                                                                                                            {{'Dropoff: '.date('Y-m-d', strtotime(dateTimeInUserTimeZone($order->schedule_dropoff, $timezone))).' '.$order->dropoff_scheduled_slot }}
                                                                                                        @else
                                                                                                            Dropoff: N/A
                                                                                                        @endif
                                                                                                    @else
                                                                                                        {{ (($order->scheduled_slot)?dateTimeInUserTimeZone($order->scheduled_date_time, $timezone,true,false,false).'. Slot: '.$order->scheduled_slot:dateTimeInUserTimeZone($order->scheduled_date_time, $timezone) ) }}
                                                                                                    @endif
                                                                                                </span>
                                                                                        @elseif(!empty($vendor->ETA))
                                                                                            @if($clientPreference->hide_order_prepare_time!=1)
                                                                                                <span class="ml-2">{{__('Your order will arrive by')}} {{ $vendor->ETA }}</span>
                                                                                            @endif
                                                                                        @endif
                                                                                        @if ($order->is_gift == '1')
                                                                                            <div class="gifted-icon">
                                                                                                <img class="p-1 align-middle"
                                                                                                    src="{{ asset('assets/images/gifts_icon.png') }}"
                                                                                                    alt="">
                                                                                                <span
                                                                                                    class="align-middle">This
                                                                                                    is a gift.</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if($clientData->socket_url !='' )
                                                                                            <a class="start_chat chat-icon btn btn-solid"  data-vendor_order_id="{{$vendor->id}}" data-vendor_id="{{$vendor->vendor_id}}" data-orderid="" data-order_id="{{$order->id}}">{{__('Chat')}}</a>
                                                                                            @if(isset($vendor->driver_chat) && ($vendor->driver_chat == 1) && ($vendor->dispatch_traking_url != ''))
                                                                                            <a class="start_chat_driver chat-icon btn btn-solid" data-driver_details_api="{{$vendor->dispatch_traking_url}}" data-vendor_order_id="{{$vendor->id}}" data-vendor_id="{{$vendor->vendor_id}}" data-orderid="" data-order_id="{{$order->id}}">{{__('Driver Chat')}}</a>
                                                                                            @endif
                                                                                        @endif

                                                                                        @if(@$order->recurring_day_data)
                                                                                            <a class="btn btn-solid recurringBtn" data-recurring_slot="{{$order->recurring_booking_time}}"  data-recurring_day_data="{{$order->recurring_day_data}}">{{getNomenclatureName('Recurring', true)}}</a>
                                                                                        @endif

                                                                                    </div>
                                                                                @endif
                                                                                <span class="left_arrow pulse"></span>
                                                                                <div class="row">
                                                                                    <div class="col-6 col-sm-3">
                                                                                        <h5 class="m-0">
                                                                                            {{ __('Order Status') }}</h5>
                                                                                        <ul class="status_box mt-1 pl-0">
                                                                                            @if (!empty($vendor->order_status))
                                                                                                <li>
                                                                                                    @if ($vendor->order_status == 'placed')
                                                                                                        <img src="{{ asset('assets/images/order-icon.svg') }}"
                                                                                                            alt="">
                                                                                                    @elseif($vendor->order_status
                                                                                                        == 'accepted')
                                                                                                        <img src="{{ asset('assets/images/payment_icon.svg') }}"
                                                                                                            alt="">
                                                                                                    @elseif($vendor->order_status
                                                                                                        == 'processing')
                                                                                                        <img src="{{ asset('assets/images/customize_icon.svg') }}"
                                                                                                            alt="">
                                                                                                    @elseif($vendor->order_status
                                                                                                        == 'out for delivery')
                                                                                                        <img src="{{ asset('assets/images/driver_icon.svg') }}"
                                                                                                            alt="">
                                                                                                    @endif
                                                                                                    <label class="m-0 in-progress">
                                                                                                    @if(@$is_exchanged_order)
                                                                                                        {{__('Exchange Order')}}
                                                                                                    @endif
                                                                                                    @if(@$order->reqCancelOrder->status == 'Pending')
                                                                                                        {{__('Cancel Order Pending')}}
                                                                                                    @else
                                                                                                        @if (@$luxury_option_name == 'Dine-In' && $vendor->order_status == 'out for delivery')
                                                                                                        {{__( ucfirst('Ready for Delivery')) }}
                                                                                                        @else
                                                                                                        {{__( ucfirst( $vendor->order_status)) }}
                                                                                                        @endif
                                                                                                        </label>
                                                                                                    @endif
                                                                                                </li>
                                                                                            @endif

                                                                                            @if (!empty($vendor->dispatch_traking_url))
                                                                                                <li>
                                                                                                    <img src="{{ asset('assets/images/order-icon.svg') }}"
                                                                                                        alt="">
                                                                                                    <a class="alOrderDetailsLink"  href="{{ route('front.booking.details', $order->order_number) }}"
                                                                                                    target="_blank">{{ __('Details') }}</a>
                                                                                                </li>
                                                                                            @endif

                                                                                            @if ($vendor->order_status_option_id==1 && ($client_preference_detail->is_cancel_order_user == 1))
                                                                                                <?php
                                                                                                    if($clientPreference->business_type == 'laundry'){
                                                                                                        $pickup_cancelling_charges = $clientCurrency->currency->symbol.$vendor->vendor->pickup_cancelling_charges;
                                                                                                    }
                                                                                                ?>

                                                                                                {{-- <h6 class="m-0">
                                                                                                @if ($clientPreference->business_type == 'laundry')
                                                                                                        <label class="rating-star cancel_order" id="cancel_order_{{$order->order_number}}" data-pickup_order="{{date('Y-m-d', strtotime(dateTimeInUserTimeZone($order->schedule_pickup, $timezone)))}}" data-order_id="{{$order->id}}" data-pickup_cancelling_charges="{{$pickup_cancelling_charges}}" data-order_number="{{$order->order_number}}" data-order_vendor_id="{{$vendor->vendor_id??0}}" data-id="{{$vendor->id??0}}">
                                                                                                            {{ __('Cancel Order') }}
                                                                                                        </label>
                                                                                                    @else
                                                                                                        <label class="rating-star cancel_order" data-order_vendor_id="{{$vendor->vendor_id??0}}" data-id="{{$vendor->id??0}}">
                                                                                                            {{ __('Cancel Orders') }}
                                                                                                        </label>
                                                                                                    @endif
                                                                                                </h6> --}}
                                                                                            @endif

                                                                                            @if ($vendor->dineInTable)
                                                                                                <li>
                                                                                                    <h5 class="mb-1">
                                                                                                        {{ __('Dine-in') }}
                                                                                                    </h5>
                                                                                                    <h6 class="m-0">
                                                                                                        {{ $vendor->dineInTableName }}
                                                                                                    </h6>
                                                                                                    <h6 class="m-0">
                                                                                                        Category :
                                                                                                        {{ $vendor->dineInTableCategory }}
                                                                                                    </h6>
                                                                                                    <h6 class="m-0">
                                                                                                        Capacity :
                                                                                                        {{ $vendor->dineInTableCapacity }}
                                                                                                    </h6>
                                                                                                </li>
                                                                                            @endif

                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class="col-6 col-sm-4">
                                                                                       <div class="product_list_item">
                                                                                        <ul
                                                                                            class="product_list_order_page product_list p-0 m-0 text-center">
                                                                                            @foreach ($vendor->products as $product)
                                                                                                @if ($vendor->vendor_id == $product->vendor_id)
                                                                                                    <li class="text-center mb-0 alOrderImg">
                                                                                                        <img src="{{ $product->image_url }}" alt="">
                                                                                                        <span class="item_no position-absolute">x{{ $product->quantity }}</span>
                                                                                                    </li>
                                                                                                    <li>
                                                                                                        <label class="items_price">
                                                                                                        {{$product->product_title}}
                                                                                                        ({{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($product->price * $clientCurrency->doller_compare)) : Session::get('currencySymbol').decimal_format($product->price * $clientCurrency->doller_compare) }})
                                                                                                        </label>
                                                                                                    </li>
                                                                                                    @php
                                                                                                        $product_total_price = $product->price * $clientCurrency->doller_compare;
                                                                                                        $product_total_count += $product->quantity * $product_total_price;
                                                                                                        $product_taxable_amount += $product->taxable_amount;
                                                                                                        $total_tax_order_price += $product->taxable_amount;
                                                                                                    @endphp
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-5 mt-md-0 mt-sm-2">
                                                                                        <ul class="price_box_bottom m-0 p-0">
                                                                                            <li
                                                                                                class="d-flex align-items-center justify-content-between">
                                                                                                <label
                                                                                                    class="m-0">{{ __('Product Total') }}</label>
                                                                                                <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($vendor->subtotal_amount
                                                                                                    *
                                                                                                    $clientCurrency->doller_compare)) : Session::get('currencySymbol').decimal_format($vendor->subtotal_amount
                                                                                                    *
                                                                                                    $clientCurrency->doller_compare)}}</span>
                                                                                            </li>
                                                                                            @if ($vendor->discount_amount > 0)
                                                                                                <li
                                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                                    <label
                                                                                                        class="m-0">{{ __('Coupon Discount') }}</label>
                                                                                                    <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($vendor->discount_amount
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)) : Session::get('currencySymbol').decimal_format($vendor->discount_amount
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)}}</span>
                                                                                                </li>
                                                                                            @endif
                                                                                            @if ($vendor->taxable_amount  > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Tax') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format(($vendor->taxable_amount) * $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format(($vendor->taxable_amount)
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif
                                                                                            
                                                                                            @if ($order->fixed_fee_amount > 0)
                                                                                                <li
                                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                                    <label
                                                                                                        class="m-0">{{ __($fixedFee) }}</label>
                                                                                                    <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($vendor->fixed_fee
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)) : Session::get('currencySymbol').decimal_format($vendor->fixed_fee
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)}}</span>
                                                                                                </li>
                                                                                            @endif

                                                                                            @if ($vendor->delivery_fee > 0)
                                                                                                <li
                                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                                    <label
                                                                                                        class="m-0">{{ __('Delivery Fee') }}</label>
                                                                                                    <span>{{$additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($vendor->delivery_fee
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)) : Session::get('currencySymbol').decimal_format($vendor->delivery_fee
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)}}</span>
                                                                                                </li>

                                                                                            @endif

                                                                                            @if ($vendor->toll_amount > 0)
                                                                                                <li
                                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                                    <label
                                                                                                        class="m-0">{{ __('Toll Fee') }}</label>
                                                                                                    <span>{{ Session::get('currencySymbol') }}{{decimal_format($vendor->toll_amount
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)}}</span>
                                                                                                </li>
                                                                                            @endif

                                                                                            @if ($vendor->service_fee_percentage_amount > 0)
                                                                                                <li
                                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                                    <label
                                                                                                        class="m-0">{{ __('Service Fee') }}</label>
                                                                                                    <span>{{ Session::get('currencySymbol') }}{{decimal_format($vendor->service_fee_percentage_amount
                                                                                                        *
                                                                                                        $clientCurrency->doller_compare)}}</span>
                                                                                                </li>
                                                                                            @endif
                                                                                            <li
                                                                                                class="grand_total d-flex align-items-center justify-content-between">
                                                                                                <label
                                                                                                    class="m-0">{{ __('Amount') }}</label>
                                                                                                @php
                                                                                                    $product_subtotal_amount = $vendor->subtotal_amount - $vendor->discount_amount + $vendor->total_container_charges +
                                                                                                 $vendor->taxable_amount + $vendor->service_fee_percentage_amount + $vendor->fixed_fee +
                                                                                                 $vendor->delivery_fee + $vendor->additional_price + $vendor->toll_amount-$order->wallet_amount_used;
                                                                                                    $subtotal_order_price += $product_subtotal_amount;
                                                                                                    
                                                                                                @endphp
                                                                                                <!-- <span>{{$additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($product_subtotal_amount
                                                                                                    *
                                                                                                    $clientCurrency->doller_compare)) : Session::get('currencySymbol').decimal_format($product_subtotal_amount
                                                                                                    *
                                                                                                    $clientCurrency->doller_compare)}}</span> -->

                                                                                                    <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->payable_amount)) : Session::get('currencySymbol') .decimal_format($order->payable_amount)}}

                                                                                            </li>

                                                                                        </ul>
                                                                                    </div>
                                                                                    <?php
                                                                                        $pkup  = json_encode(date('Y-m-d', strtotime(dateTimeInUserTimeZone($order->schedule_pickup, $timezone))));
                                                                                        $dpoff = json_encode(date('Y-m-d', strtotime(dateTimeInUserTimeZone($order->schedule_dropoff, $timezone))));
                                                                                        $rescheduling_charges = json_encode($clientCurrency->currency->symbol.$vendor->vendor->rescheduling_charges);
                                                                                        $pickup_cancelling_charges = json_encode($clientCurrency->currency->symbol.$vendor->vendor->pickup_cancelling_charges);
                                                                                        $newPickupClass = json_encode('pickup_'.$order->order_number);
                                                                                        $newDropoffClass = json_encode('dropoff_'.$order->order_number);
                                                                                    ?>
                                                                                    <!-- @if ($clientPreference->business_type == 'laundry') -->
                                                                                    {{-- Model to get Slots --}}
                                                                                    <form onsubmit='checkDates({{$pkup}}, {{$dpoff}}, {{$rescheduling_charges}}, {{$pickup_cancelling_charges}}, {{$newPickupClass}}, {{$newDropoffClass}})' action="{{route('user.rescheduleOrder')}}" class="rescheduleOrder" id="rescheduleOrder" name="rescheduleOrder" method="post">
                                                                                        @csrf
                                                                                        <div class="modal fade" id="orderModel{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header" style="border-bottom: 1px solid #e5e8eb;">
                                                                                                    <h5 class="modal-title text-capitalize" id="exampleModalLongTitle">Order Number: #{{$order->order_number}}</h5>
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                        <input type="hidden" name="order_id" value="{{Crypt::encrypt($order->id)}}">
                                                                                                        @include('frontend.modals.rescheduling_modal', ['newPickupClass' => 'pickup_'.$order->order_number , 'newDropoffClass' => 'dropoff_'.$order->order_number ])
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                    <button type="submit"  class="btn btn-solid reschedule_now_btn">Reschedule Now</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                    {{-- Model to get Slots --}}
                                                                                <!-- @endif -->

                                                                                </div>
                                                                                @if( isset($vendor->dispatcher_status_icons) && count($vendor->dispatcher_status_icons) > 0)
                                                                                <div class="step-indicator step-indicator-order">
                                                                                    @foreach ($vendor->dispatcher_status_icons as $key => $icons)
                                                                                    @if(isset($vendor['vendor_dispatcher_status'][$key]) && !empty($vendor['vendor_dispatcher_status'][$key]))
                                                                                    <div class="step step{{$key+1}} active">
                                                                                        <div class="step-icon-order step-icon-order-fill"><img src="{{@$vendor->vendor_dispatcher_status[$key]->status_data['icon']}}"></div>
                                                                                    </div>

                                                                                    <div class="indicator-line active"></div>

                                                                                    @if(count($vendor['vendor_dispatcher_status']) == $key+1)
                                                                                        <p>{{@$vendor->vendor_dispatcher_status[$key]->status_data['driver_status']}}</p>
                                                                                    @endif
                                                                                    @if($key < count($vendor->dispatcher_status_icons)-1)
                                                                                    <div class="indicator-line active"></div>
                                                                                    @endif
                                                                                    @else
                                                                                    <div class="step step{{$key+1}} @if(app('request')->input('step') >= '1' || empty(app('request')->input('step'))) active @endif">
                                                                                        <div class="step-icon-order"><img src="{{$icons}}"></div>
                                                                                        <p></p>
                                                                                    </div>
                                                                                    @if($key < count($vendor->dispatcher_status_icons)-1)
                                                                                    <div class="indicator-line"></div>
                                                                                    @endif
                                                                                    @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                @endif

                                                                                <div class="row">
                                                                                    <div class="col-12 d-flex align-items-center justifiy-content-end alListBtnGroups">
                                                                                    @if ($vendor->order_status_option_id==1 && ($client_preference_detail->is_cancel_order_user == 1))
                                                                                            <?php
                                                                                            if($clientPreference->business_type == 'laundry'){
                                                                                                $pickup_cancelling_charges = $clientCurrency->currency->symbol.$vendor->vendor->pickup_cancelling_charges;
                                                                                            }
                                                                                        ?>
                                                                                            <div>
                                                                                               @if ($clientPreference->business_type == 'laundry')
                                                                                                    <label class="rating-star cancel_order btn btn-solid" id="cancel_order_{{$order->order_number}}" data-pickup_order="{{date('Y-m-d', strtotime(dateTimeInUserTimeZone($order->schedule_pickup, $timezone)))}}" data-order_id="{{$order->id}}" data-pickup_cancelling_charges="{{$pickup_cancelling_charges}}" data-order_number="{{$order->order_number}}" data-order_vendor_id="{{$vendor->vendor_id??0}}" data-id="{{$vendor->id??0}}">
                                                                                                        {{ __('Cancel Order') }}
                                                                                                    </label>
                                                                                                @else
                                                                                                    <label class="rating-star cancel_order btn btn-solid" data-order_vendor_id="{{$vendor->vendor_id??0}}" data-id="{{$vendor->id??0}}">
                                                                                                        {{ __('Cancel Order') }}
                                                                                                    </label>
                                                                                                @endif
                                                                                            </div>
                                                                                            @elseif($vendor->order_status_option_id==2 && $client_preference_detail->is_cancel_order_user == 1 && $vendor->vendor->cancel_order_in_processing == 1)
                                                                                                @if(empty($order->reqCancelOrder))
                                                                                                    <label class="rating-star request_cancel_order btn btn-solid" data-order_vendor_id="{{$vendor->order_id??0}}" data-id="{{$vendor->id??0}}" data-vendor_id="{{$vendor->vendor_id??0}}" style="width: auto;display: inline-block;">
                                                                                                        {{ __('Cancel Order') }}
                                                                                                    </label>
                                                                                                @elseif($order->reqCancelOrder->status == 'Rejected')
                                                                                                    <li class="bg-txt" style="margin-top: 10px;"><span class="badge badge-danger mr-2" style="font-size:12px">{{ __('Cancel Order Rejected') }}</span><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" aria-hidden="true" data-original-title="{{$order->reqCancelOrder->vendor_reject_reason??''}}"></i></li>
                                                                                                @endif
                                                                                            @endif
                                                                                            {{-- Check if order is created only --}}
                                                                                            @if ($vendor->status == 0)
                                                                                                @if ($vendor->order_status == 'placed')
                                                                                                    <button  data-toggle="modal" data-target="#orderModel{{$order->id}}" class="reschedule_order btn btn-solid" data-id="{{$order->id}}" data-order_vendor_id="{{ $vendor->id ?? 0 }}" data-vendor_id="{{$vendor->id}}">Reschedule</button>
                                                                                                @endif
                                                                                            @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        @endforeach

                                                                    </div>
                                                                    <div class="col-md-3 mb-3 pl-lg-0">
                                                                        <div class="card-box p-2 mb-0 h-100">
                                                                            <ul class="price_box_bottom m-0 pl-0 pt-1">
                                                                                <li
                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                    <label
                                                                                        class="m-0">{{ __('Sub Total') }}</label>
                                                                                    <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->total_amount
                                                                                        *
                                                                                        $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->total_amount
                                                                                        *
                                                                                        $clientCurrency->doller_compare)}}</span>
                                                                                </li>
                                                                                @if ($order->wallet_amount_used > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Wallet') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->wallet_amount_used
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->wallet_amount_used
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif

                                                                                @if ($order->loyalty_amount_saved > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Loyalty Used') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->loyalty_amount_saved
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->loyalty_amount_saved
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif
                                                                                @if ($total_other_taxes > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Tax') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format(($total_other_taxes) * $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format(($total_other_taxes)
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif
															
                                                                                @if ($order->total_container_charges > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Container Charges') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format(($vendor->total_container_charges) * $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format(($vendor->total_container_charges) * $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif

                                                                                @if ($order->total_toll_amount > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Toll Amount') }}</label>
                                                                                        <span>{{ Session::get('currencySymbol') }}{{decimal_format($order->total_toll_amount
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif
                                                                                @if ($order->total_service_fee > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Service Fee') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->total_service_fee
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->total_service_fee
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif

                                                                                @if ($order->fixed_fee_amount > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __($fixedFee) }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->fixed_fee_amount
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->fixed_fee_amount
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif
                                                                                @if ($order->tip_amount > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Tip Amount') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->tip_amount
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->tip_amount
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif


                                                                                @if ($order->total_delivery_fee > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Delivery Fee') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->total_delivery_fee
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->total_delivery_fee
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif

                                                                                @if ($order->total_discount_calculate > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Discount') }}</label>
                                                                                        <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->total_discount_calculate
                                                                                            *
                                                                                            $clientCurrency->doller_compare)) : Session::get('currencySymbol') .decimal_format($order->total_discount_calculate
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif

                                                                                @if ( checkColumnExists('orders', 'gift_card_amount') &&  $order->gift_card_amount > 0)
                                                                                    <li
                                                                                        class="d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Gift Card Amount') }}</label>
                                                                                        <span>{{ Session::get('currencySymbol') }}{{decimal_format($order->gift_card_amount
                                                                                            *
                                                                                            $clientCurrency->doller_compare)}}</span>
                                                                                    </li>
                                                                                @endif

                                                                                <li class="grand_total d-flex align-items-center justify-content-between">
                                                                                    <label
                                                                                        class="m-0">{{ __('Total Payable') }}</label>
                                                                                    <span>{{ $additionalPreference["is_token_currency_enable"] ? getInToken(decimal_format($order->payable_amount)) : Session::get('currencySymbol') .decimal_format($order->payable_amount)}}

                                                                                    @if(!checkColumnExists('orders', 'is_postpay'))
                                                                                        $order->is_postpay = 0;
                                                                                    @endif

                                                                                    @if ($order->payment_option_id != 1 && $order->is_postpay == 1 && $order->payment_status == 0)
                                                                                        <br/><span style="color:var(--theme-deafult);">Unpaid</span>
                                                                                    @endif
                                                                                    </span>
                                                                                </li>


                                                                                <li class="grand_total d-flex align-items-center justify-content-between">
                                                                                 {{createStringQR($order->order_number,$size=200);}}
                                                                                </li>

                                                                                {{-- mohit sir branch code added by sohail --}}
                                                                                @if (@$order->advance_amount > 0)
                                                                                    <li
                                                                                        class="grand_total d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Advance Paid') }}</label>
                                                                                        <span>{{ Session::get('currencySymbol') }}{{ decimal_format(@$order->advance_amount) }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="grand_total d-flex align-items-center justify-content-between">
                                                                                        <label
                                                                                            class="m-0">{{ __('Pending Amount') }}</label>
                                                                                        <span>{{ Session::get('currencySymbol') }}{{ decimal_format($order->payable_amount) - decimal_format(@$order->advance_amount) }}</span>
                                                                                    </li>
                                                                                @endif
                                                                                {{-- till here --}}
                                                                                @if ($order->payment_option_id != 1 && $order->is_postpay == 1 && $order->payment_status == 0)
                                                                                <!-- <li class="align-items-center justify-content-between w-100">
                                                                                    <button id="amount_pay_now" class="btn btn-solid w-100" type="button" data-paymentoptionid="{{$order->payment_option_id}}" data-orderid="{{$order->id}}" data-payableamount="{{decimal_format($order->payable_amount+$order->fixed_fee_amount)}}">Pay Now</button>
                                                                                </li> -->
                                                                                @endif
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="col-12">
                                                            <div class="no-gutters order_head">
                                                                <h4 class="text-center">{{ __('No Active Order Found') }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                {{ $activeOrders->appends(['pageType' => 'activeOrders'])->links() }}
                                            </div>
