
<link rel="stylesheet" href="{{ asset('css/rental.css') }}">
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>


<section class="banner">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="text">
                    <h1>Seamless Journeys, Unforgettable Rides </h1>
                    <p>Your Trusted Car Rental and Airport Transfer Solution.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="image">
                    <img src="yacht-images/banner.png" alt="banner">
                </div>
            </div>
        </div>
        <div class="banner_tab">
            <div class="tabs">
                {{-- <ul id="tabs-nav" class="d-flex align-items-center">
				    <li>
				    	<a href="#tab1">
				    		<img src="yacht-images/icons/1.png" alt="">
				    		Car Rental
				    	</a>
				    </li>
				    <li>
				    	<a href="#tab1">
				    		<img src="yacht-images/icons/2.png" alt="">
				    		Airport Pick and Drop
				    	</a>
				    </li>
					<li>
				    	<a href="#tab1">
				    		<img src="yacht-images/icons/2.png" alt="">
				    		Yacht Pick and Drop
				    	</a>
				    </li>				    
				  </ul> <!-- END tabs-nav --> --}}
                <form action="{{ route('productSearch') }}" method="GET">
                    @csrf

                    <div class="tab">
                        <div class="d-flex align-items-center">
                            <div class="form-group">
                                <input type="radio" value="rental" name="service" placeholder="" id="car" checked>
                                <label for="car">
                                    <img src="yacht-images/icons/1.png" alt="">
                                    Car Rental
                                </label>
                                <span></span>
                            </div>
                            {{-- <div class="form-group">
                                <input type="radio" value="airport" name="service" placeholder="" id="Airport">
                                <label for="Airport">
                                    <img src="yacht-images/icons/2.png" alt="">
                                    Airport Pick and Drop
                                </label>
                                <span></span>
                            </div> --}}
                            {{-- <div class="form-group">
                                <input type="radio" value="yacht" name="service" placeholder="" id="Yacht">
                                <label for="Yacht">
                                    <img src="yacht-images/icons/2.png" alt="">
                                    Yacht Pick and Drop
                                </label>
                                <span></span>
                            </div> --}}
                        </div>
                    </div>
                    <div id="tabs-content">
                        <div id="tab1" class="tab-content">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="item">
                                        <h5>Pickup Location</h5>
                                        <p><img src="yacht-images/icons/3.png" alt="">
                                            <input class="" type="text" name="pickup_location" id="pickup_location" value="" placeholder="1801 Oak Ridge Ln" required>
                                            <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="">
                                            <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="">
                                        </p>
                                    </div>
                                   
                                </div>
                                <div class="col" id="dropoff-box" style="display:none;">
                                    <div class="item">
                                        <h5>Return Location</h5>
                                        <p><img src="yacht-images/icons/3.png" alt="">
                                            <input type="text" name="drop_location" id="drop_location" value="" placeholder="1801 Oak Ridge Ln">
                                            <input type="hidden" name="drop_longitude" id="drop_longitude" value="">
                                            <input type="hidden" name="drop_latitude" id="drop_latitude" value="">
                                        </p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="item">
                                        <h5>Pickup Dropoff Date & Time</h5>
                                        <p><img src="yacht-images/icons/4.png" alt="">
											<input type="text" id="range-datepicker" class="form-control flatpickr-input" placeholder="2018-10-03 to 2018-10-10" name="pick_drop_time" required="required"/>
                                            {{-- <input type="datetime-local" name="pickup_time"> --}}
                                        </p>
                                    </div>
                                </div>
                                
                                    {{-- <div class="col d-none">
                                    <div class="item">
                                        <h5>Drop Date & Time</h5>
                                        <p><img src="yacht-images/icons/4.png" alt="">
                                            <input type="datetime-local" name="drop_time">
                                        </p>
                                    </div>
                                </div> --}}
                                <div class="col" style="display: none" id="seats_div">
                                    <div class="item">
                                        <h5>Seat Number</h5>
                                        <p>
                                            <input class="pl-0" type="number" name="seats" value="" placeholder="04">
                                        </p>
                                    </div>
                                </div>
                                <div class="col">
                                    <label style="visibility: hidden;"> submit</label>
                                    <div class="cta">
                                        <button type="submit" class="border-0">Search</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col">
                                <div class="form-group" id="diff-box">
                                        <input type="checkbox" name="diff_location" id="diff-location" />
                                        <label for="diff-location" class="different_cta">Different Return Location</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('layouts.store.remove_cart_model')
</section>

@section('script')
{{-- <script src="{{asset('js/custom.js')}}"></script>
<script src="{{asset('js/location.js')}}"></script>
<script src="{{asset('assets/libs/moment/moment.min.js')}}"></script>
<script src="{{asset('assets/libs/datetimepicker/daterangepicker.min.js')}}" ></script>
<script src="{{ asset('js/storage/OrderStorage.js') }}"></script>
<script src="{{ asset('assets/js/alert/alert.js') }}"></script>
<script src="{{ asset('assets\js\backend\backend_common.js') }}"></script>
<script src="{{asset('front-assets/js/underscore.min.js')}}"></script>
<script defer type="text/javascript" src="{{asset('front-assets/js/popper.min.js')}}"></script>
<script defer type="text/javascript" src="{{asset('front-assets/js/menu.js')}}"></script>
<script defer type="text/javascript" src="{{asset('front-assets/js/lazysizes.min.js')}}"></script>
<script defer type="text/javascript" src="{{asset('front-assets/js/bootstrap.js')}}"></script>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script> --}}
<script type="text/javascript"src="{{asset('front-assets/js/slick.js')}}"></script>
{{-- <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
<script type="text/javascript" src="{{asset('front-assets/js/jquery.elevatezoom.js')}}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script defer type="text/javascript" src="{{asset('front-assets/js/script.js')}}"></script>
{{-- <script>
    var cart_product_url= "{{ route('getCartProducts') }}";
    var delete_cart_product_url= "{{ route('deleteCartProduct') }}";
    var digit_count = "{{$client_preference_detail->digit_after_decimal}}";
    var show_cart_url = "{{ route('showCart') }}";
    var vendor_type = "delivery";
    var currentRouteName = "{{Route::currentRouteName()}}";
    var is_service_product_price_from_dispatch_forOnDemand = 0;
    var url2 = "{{ route('config.get') }}";
    var featured_products_length = '';
    let is_map_search_perticular_country = '';
    var check_isolate_single_vendor_url = "{{ route('checkIsolateSingleVendor') }}";
    let stripe_publishable_key = '{{ $stripe_publishable_key }}';
    let stripe_fpx_publishable_key = '{{ $stripe_fpx_publishable_key }}';
    let stripe_ideal_publishable_key = '{{ $stripe_ideal_publishable_key }}';
    @if(Session::has('vendorType') && (Session::get('vendorType') != '') )
        vendor_type = "{{Session::get('vendorType')}}";
    @endif
    var NumberFormatHelper = { formatPrice: function(x,format=1){
    if(x){
        if(digit_count)
        {
            x = parseFloat(x).toFixed(digit_count);
        }
        if(format == 1)
        {
            var parts = x.split(".");
            return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ((parts[1] !== undefined) ? "." + parts[1] : "");
        }
    }
    return x;
    }
    };

     var userLatitude = "{{ session()->has('latitude') ? session()->get('latitude') : 0 }}";
    var userLongitude = "{{ session()->has('longitude') ? session()->get('longitude') : 0 }}";

    if(!userLatitude || userLongitude ==0 || userLongitude==''){
        @if(!empty($client_preference_detail->Default_latitude))
            userLatitude = "{{$client_preference_detail->Default_latitude}}";
        @endif
    }
    if(!userLatitude ){
        userLatitude = "30.7333";
    }

    if(!userLongitude || userLongitude ==0 || userLongitude==''){
        @if(!empty($client_preference_detail->Default_longitude))
             userLongitude = "{{$client_preference_detail->Default_longitude}}";
        @endif
    }
    if(!userLongitude ){
        userLongitude = "76.7794";
    }

    @if(Session::has('selectedAddress'))
        selected_address = 1;
    @endif

        var bindLatlng, bindmapProp, bindMap = '';
    function bindLatestCoords(userLatitude, userLongitude){
        bindLatlng = new google.maps.LatLng(userLatitude, userLongitude);
        bindmapProp = {
            center:bindLatlng,
            zoom:13,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };
        bindMap=new google.maps.Map(document.getElementById("nearmap"), bindmapProp);
    }
    bindLatestCoords(userLatitude, userLongitude);
</script> --}}
  <script src="{{ asset('js/car-rental.js') }}"></script>
@endsection