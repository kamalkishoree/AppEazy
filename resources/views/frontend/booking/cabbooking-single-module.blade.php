 <!-- Cab Booking Start From Here -->
 @php
     if (isset($homePageLabel->image['proxy_url']) && !empty($homePageLabel->image['proxy_url'])) {
         $img = $homePageLabel->image['proxy_url'] . '1900/500' . $homePageLabel->image['image_path'];
     } else {
         $img = asset('images/CabBANNER.jpg');
     }

    $dropLocation = getNomenclatureName('Enter Drop Location', true);
    $dropLocation = ($dropLocation=="Enter Drop Location")?__('Enter Drop Location'):__($dropLocation);

                
 @endphp

 <section class="cab-banner-area alTaxiBannerStart"
     style="background:url({{ $img }});background-size: cover;background-repeat: no-repeat;background-position: center;">
     <div class="container-fluid  p-64 py-64">
         <div class="row">
             <div class="col-md-8 col-lg-5 col-xl-4">
                 <div class="card-box mb-0">
                     <h2>{{ $homePageLabel->translations->first() ? $homePageLabel->translations->first()->title : '' }}
                     </h2>
                     <form
                         action="{{ route('categoryDetail', $homePageLabel->pickupCategories->first()->categoryDetail->slug ?? '') }}"
                         class="cab-booking-form">
                         <div class="cab-input">
                             <div class="form-group mb-1 position-relative"> <input class="form-control edit-other-stop"
                                     type="text" placeholder="{{ __('Enter Pickup Location') }}"
                                     name="pickup_location" id="pickup_location_{{ $key }}"
                                     data-rel="{{ $key }}"> <input type="hidden"
                                     name="pickup_location_latitude" value=""
                                     id="pickup_location_{{ $key }}_latitude_home"
                                     data-rel="{{ $key }}" /> <input type="hidden"
                                     name="pickup_location_longitude" value=""
                                     id="pickup_location_{{ $key }}_longitude_home"
                                     data-rel="{{ $key }}" /> </div>
                                    <div class="form-group mb-0"> <input class="form-control edit-other-stop" type="text"
                                     name="destination_location" placeholder="{{$dropLocation}}"
                                     id="destination_location_{{ $key }}" data-rel="{{ $key }}">
                                    <input type="hidden" name="destination_location_latitude" value=""
                                     id="destination_location_{{ $key }}_latitude_home"
                                     data-rel="{{ $key }}" /> <input type="hidden"
                                     name="destination_location_longitude" value=""
                                     id="destination_location_{{ $key }}_longitude_home"
                                     data-rel="{{ $key }}" /> </div>
                             <div class="input-line"></div>
                         </div>
                         <div class="cab-footer"> <button
                                 class="btn btn-solid new-btn request-btn">{{ __('Request Now') }}</button> <button
                                 class="btn btn-solid new-btn schedule-btn">{{ __('Schedule For Later') }}</button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <!-- Cab Content Area Start From Here -->
