@extends('layouts.store', [
'title' => 'test',
])
@section('content')
<section class="current_location">
    <div class="container">
        <div class="serch_result_heading">
            <div class="item">
                <h3>{{$pickup->address ?? session('selectedAddress')}}</h3>
                <span><i class="fa fa-pencil filter_cta"></i></span>
            </div>
            <div class="result_date">
                <p>
                    <span>{{date('d M Y H:i', strtotime($pickup_time))}} </span>
                    -
                    <span>{{date('d M Y H:i', strtotime($drop_time))}} </span>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="result_item">
    <div class="container">
        <div class="heading d-flex align-items-center justify-content-between">
            @if (!empty($products)) 
           
            @if($products->count())
            <h2>Available {{$service == 'yacht' ? ucfirst($service) : 'Cars'}}</h2>
            @endif
            @else
            <h2>No Available {{$service == 'yacht' ? ucfirst($service) : 'Cars'}}</h2>
            @endif
            <span class="filter_cta">
                <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect width="29" height="29" fill="url(#pattern0)" />
                    <defs>
                        <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_4771_3915" transform="scale(0.00195312)" />
                        </pattern>
                        <image id="image0_4771_3915" width="512" height="512" xlink:href="data:image/png;base64,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" />
                    </defs>
                </svg>
            </span>
        </div>
        <div class="row">
            @if(!empty($products))
            @foreach ($products as $product)
            @php
            if ($service == 'airport') {
            $link = 'category/airport?destination_location='.$product['location'].'&destination_location_latitude='.$product['latitude'].'&destination_location_longitude='.$product['longitude'];
            $imgSrc = $product['path'];
            $productTitle = $product['title'];
            }else{
            $link = $product->vendor->slug.'/product/'.$product->url_slug.'?pickup='.$pickup_time.'&drop='.$drop_time;
            if(count($product->pimage)){
            $img = $product->pimage[0]->image;
            $defaultImg = 0;
            }else{
            $img = loadDefaultImage();
            $defaultImg = 1;
            }
            $imgSrc = $defaultImg ? $img : get_file_path(@$img->path['image_fit'].'500/500'.@$img->path['image_path'] ?? '','FILL_URL','330','330');
            $productTitle = $product['title'];
            $fields = [];
            foreach ($product->ProductAttribute as $productAttribute) {
                if ($productAttribute->attributeOption()->exists()) {
                    if(!empty($title = $productAttribute->attributeOption->title)){
                            $fields[$productAttribute->key_name] = $title;
                        }else{
                            $fields[$productAttribute->key_name] = $productAttribute->key_value;
                        }
                    }
                }
            }

            $allReviews = array_column($product->vendor->products()->with('reviews')->get()->toArray(),'reviews');
    
            $rating = array_sum(array_column($allReviews,'rating'));
            @endphp
            <div class="col-md-3">
                <div class="item">
                    <a class="common-product-box text-center" href="{{ $link }}" target="_blank">
                        <div class="image">
                            <img src="{{$imgSrc}}" data-src="{{$imgSrc}}" alt="" />
                        </div>
                    </a>
                    <div class="text">
                        <div class="product_heading">
                            <h3>{{$productTitle}}</h3>
                            <span><i class="fa fa-star"></i>({{$rating??0}})</span>
                        </div>
                        <div class="productList d-flex justify-content-between">
                            <ul class="product-features">
                                @if($service == 'rental')
                                <li><a href="javascript:void(0);">{{$fields['Transmission'] ?? ''}}</a></li>
                                <li><a href="javascript:void(0);">{{$fields['Fuel Type'] ?? ''}}</a></li>
                                <li><a href="javascript:void(0);">{{$fields['Seats'] ?? '0'}} Seats</a></li>
                                @elseif($service == 'yacht')
                                <li><a href="javascript:void(0);">{{$fields['Cabins'] ?? '0'}} Cabins</a></li>
                                <li><a href="javascript:void(0);">{{$fields['Baths'] ?? '0'}} Baths</a></li>
                                <li><a href="javascript:void(0);">{{$fields['Berths'] ?? '0'}} Berths</a></li>
                                @endif
                            </ul>
                            <span class="yacht-location" title="{{$product->vendor->address}}">
                                {{substr($product->vendor->address, 0, 15) . '...'}}
                            </span>
                        </div>
                        <div class="productPrice d-flex justify-content-between align-items-center">
                            <div class="left">
                                <span class="w-100 price">{{Session::get('currencySymbol')}}{{decimal_format($product->variant[0]->price)}}</span>
                                <span class="off_price d-none">$ 7,987 total</span>
                            </div>
                            <div class="right">
                                <span>{{number_format(($product->vendor->distance_in_meter/1000),2)}} Km away</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
            @endif

        
        </div>
    </div>
    </div>
</section>

<div class="filter_bar">
    <span class="close close_filter"><i class="fa fa-close"></i></span>
    <div class="filter_item">
        <h3>Location</h3>
        <form action="" method="GET">
            @csrf
            <div class="form-group">
                <label>Pickup Location</label>
                <input type="text" value="{{$pickup->address ?? Session::get('selectedAddress')}}" name="pickup_location" id="pickup_location">
                <input type="hidden" value="{{$pickup->longitude ?? Session::get('longitude')}}" name="pickup_longitude" id="pickup_longitude">
                <input type="hidden" value="{{$pickup->latitude ?? Session::get('latitude')}}" name="pickup_latitude" id="pickup_latitude">
                <input type="hidden" value="{{$service}}" name="service">
            </div>
            @if($service == 'rental')
                <div class="form-group" id="dropoff-box" @if(!$diff_location) style="display:none;" @endif>
                    <label>Return Location</label>
                    <input type="text" value="{{$dropoff->address}}" name="drop_location" id="drop_location" placeholder="1801 Oak Ridge Ln">
                    <input type="hidden" value="{{$dropoff->latitude}}" name="drop_latitude" id="drop_latitude">
                    <input type="hidden" value="{{$dropoff->longitude}}" name="drop_longitude" id="drop_longitude">
                </div>
                <div class="diff-loc">
                    <input type="checkbox" name="diff_location" id="diff-location" @if($diff_location) checked @endif />
                    <label for="diff-location">Different Return Location</label>
                </div>
            @endif
            @if($service == 'yacht')
            <div class="form-group">
                <label>Seats</label>
                <input type="number" name="seats" value="{{$seats}}" placeholder="04" required>
            </div>
            @endif
            <div class="form-group">
                <label>Pickup Dropoff Date & Time</label>
                {{-- <input type="datetime-local" name="drop_time" value="{{$drop_time}}" min="{{date("Y-m-d\TH:i")}}"> --}}
                <input type="text" id="range-datepicker" name="pick_drop_time" class="form-control flatpickr-input" placeholder="2018-10-03 to 2018-10-10" value="{{$pick_drop_time}}">
            </div>
            <div class="form-cta">
                <input type="submit" value="submit">
            </div>
        </form>
       
    </div>
</div>
@endsection
