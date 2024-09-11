@extends('layouts.store', ['title' => __('Home')])
@section('css-links')
<link rel="stylesheet" href="{{ asset('assets/css/intlTelInput.css') }}">
{{--<link href="{{asset('css/aos.css')}}" rel="stylesheet">--}}
@endsection
@section('css')
<style>
.logoArea_bar{height:300px;margin: 0 0 30px;}
.alMainMenuView{height:100px;width: 100px;border-radius: 50%;}
@media(max-width: 767px){.logoArea_bar{height:150px;margin: 0 0 20px;}}
</style>
<style type="text/css">
        .file>label,
        .file.upload-new>label {
            width: 100%;
            border: 1px solid #ddd;
            padding: 30px 0;
            height: 216px;
        }

        .file .update_pic img,
        .file.upload-new img {
            height: 130px;
            width: auto;
        }

        .update_pic,
        .file.upload-new .update_pic {
            width: 100%;
            height: auto;
            margin: auto;
            text-align: center;
            border: 0;
            border-radius: 0;
        }

        .file--upload>label {
            margin-bottom: 0;
        }

        .errors {
            color: #F00;
            background-color: #FFF;
        }
        .al_body_template_one .iti__selected-flag{
            height:auto;
            padding: 10px 6px;
        }

    </style>
@endsection
@section('content')
<!-- Shimmer Efferct Start -->

@if ($auth_user && $auth_user->is_phone_verified == 0)
<div class="modal fade" id="phoneVerificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Verify Your Phone Number</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
  
      <section class="wrapper-main  alSectionTop  align-items-center">
        <div class="container sendOtpform">
            <div class="row">
                <div class="col-lg-12 mb-lg-0 text-center">
                    <h3 class="mb-2">{{ __('Verify Phone Number') }}</h3>
                    <div class="row mt-3">
                        <div class="{{ (@session('preferences')->concise_signup == 1)? 'mx-auto':'offset-xl-2 col-xl-8 text-left' }}">
                            <form name="verifyuserphone" id="verifyuserphone" enctype="multipart/form-data" action="#"
                                class="px-lg-4" method="post"> @csrf
                                   
                                    <div class="col-md-12 ">
                                        <label for="">{{ __('Phone No.') }}</label>
                                        <input type="tel"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone" placeholder="{{ __('Phone No.') }}" name="phone_number"
                                            value="{{ old('full_number') }}">

                                        <input type="hidden" id="dialCode" name="dialCode"
                                            value="{{ old('dialCode') ? old('dialCode') : Session::get('default_country_phonecode', '1') }}">
                                        <input type="hidden" id="countryData" name="countryData"
                                            value="{{ old('countryData') ? old('countryData') : Session::get('default_country_code', 'US') }}">
                                            <span class="invalid-feedback" role="alert" style="display:block">
                                                <strong></strong>
                                            </span>
										<div class="row  my-3">
                                    <div class="col-md-12">
                                        <input type="hidden" name="device_type" value="web">
                                        <input type="hidden" name="device_token" value="web">
                                        <button type="submit"
                                            class="btn btn-solid submitVerifyuser w-100">{{ __('Submit') }}</button>
                                    </div>
                                </div>
                            </form>
                          </div>
                    </div>
                </div>
            </div>
	  </div>
		<div class="container verifyotpform d-none">
            <div class="row">
                <div class="col-lg-12 mb-lg-0 text-center">
                    <h3 class="mb-2">{{ __('Verify OTP') }}</h3>
                    <div class="row mt-3">
                        <div class="{{ (@session('preferences')->concise_signup == 1)? 'mx-auto':'offset-xl-2 col-xl-8 text-left' }}">

					@if($clientPreferences->verify_phone == 1 && $auth_user->is_phone_verified == 0)
        		    <div class="col-lg-12 text-center {{$auth_user->verify_phone == 1 && $clientPreferences->is_phone_verified == 0 ? '' : 'offset-lg-0'}}" id="verify_phone_main_div">
              	  @if($auth_user->is_phone_verified == 0)
                <img src="{{asset('front-assets/images/phone-otp.svg')}}">
                <h3 class="mb-2">{{__('Verify Phone')}}</h3>
                <p>{{__('Enter the code we just sent you on your phone number')}}</p>
                <div class="row">
                    <div class="col-md-12 col-xl-12 text-left">
                        <div class="verify_id input-group mb-3 radius-flag">
						<form name="verifyuserOtp" id="verifyuserOtp" enctype="multipart/form-data" action="#"  class="" method="post"> @csrf
							<!-- <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone" placeholder="{{ __('Phone No.') }}" name="phone_number" value="{{ old('full_number') }}">                     -->
							<div class="col-md-12 ">
                                        <label class="d-none"for="">{{ __('Phone No.') }}</label>
                                        <input type="tel"
                                            class="d-none form-control @error('phone_number') is-invalid @enderror"
                                            id="phone2" placeholder="{{ __('Phone No.') }}" name="phone_number"
                                            value="{{ old('full_number') }}">
											
                                        <input type="hidden" id="dialCode2" name="dialCode"
                                            value="">
                                        <input type="hidden" id="countryData" name="countryData"
                                            value="{{ old('countryData') ? old('countryData') : Session::get('default_country_code', 'US') }}">
							</div>  							
							<!-- <div class="input-group-append position-absolute position-right">
                                <a class="input-group-text" id="edit_phone" href="javascript:void(0)">{{__('Edit')}}</a>
                            </div> -->
                            <span class="valid-feedback d-block text-center" role="alert">
                                <strong class="edit_phone_feedback"></strong>
                            </span>
                        </div>
                        <div method="get" class="digit-group otp_inputs d-flex justify-content-between" data-group-name="digits" data-autosubmit="false" autocomplete="off">
                            <input class="form-control" type="text" id="digit-1" name="digit-1" data-next="digit-2" onkeypress="return isNumberKey(event)"/>
                            <input class="form-control" type="text" id="digit-2" name="digit-2" data-next="digit-3" data-previous="digit-1" onkeypress="return isNumberKey(event)"/>
                            <input class="form-control" type="text" id="digit-3" name="digit-3" data-next="digit-4" data-previous="digit-2" onkeypress="return isNumberKey(event)"/>
                            <input class="form-control" type="text" id="digit-4" name="digit-4" data-next="digit-5" data-previous="digit-3" onkeypress="return isNumberKey(event)"/>
                            <input class="form-control" type="text" id="digit-5" name="digit-5" data-next="digit-6" data-previous="digit-4" onkeypress="return isNumberKey(event)"/>
                            <input class="form-control" type="text" id="digit-6" name="digit-6" data-next="digit-7" data-previous="digit-5" onkeypress="return isNumberKey(event)"/>
                        </div>
                        <div class="row text-center mt-2">
                         @if($staticOtpEnable)
                                    <div class="col-12">   
                                            <div class="text-success">Your otp is 123456</div>
                                    </div>
                           @endif
                           
                            <div class="col-md-12 mt-3">
							         <span class="invalid_phone_otp_error invalid-feedback2 w-100 d-block text-center text-danger"></span>
							           <button type="button" class="btn btn-solid" id="verify_phone_token">{{__('VERIFY')}}</button>
										<button type="button" class="btn btn-solid" id="back_send_otp">{{__('BACK')}}</button>

									</div>
						    </form>	
								</div>
							</div>
							     <div class="after-success-otp d-none">
							       <img src="{{asset('front-assets/images/verified.svg')}}" alt="">
									<h3 class="mb-2">{{__('Phone Verified!')}}</h3>
									<p>{{__('You have successfully verified the')}} <br> {{__('Phone.')}}</p>
									
									</div>
									
								</div>
							</div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
      </section>
      </div>
    </div>
  </div>


@section('script')
    <script src="{{ asset('assets/js/intlTelInput.js') }}"></script>
    <script src="{{asset('js/phone_number_validation.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
    <script>
        $(document).ready(function() {
            @if (session('preferences'))
                @if(@session('preferences')->concise_signup == 1)
                    $('#phone').change(function() {
                        var custPhone = $(this).val();
                        $('#guest-email').val(custPhone+'@gmail.com');
                    });
                @endif
            @endif

            jQuery.validator.addMethod("indianMobile", function(value, element) {
                var dialCode = $("#dialCode").val();
                // Regular expression for Indian mobile numbers
                if(dialCode == 91) {
                    var regex = /^[6-9]\d{9}$/;
                    return this.optional(element) || regex.test(value);
                } else {
                    return true;
                }
                
                }, "Please enter a valid Indian mobile number.");

            jQuery.validator.addMethod("alphanumeric", function(value, element) {
                return this.optional(element) || /^[a-zA-Z0-9 ]+$/i.test(value);
            }, "Name should contains alphanumeric data.");
            $("#register").validate({
                errorClass: 'errors',
                rules: {
                    name : {
                        required: true,
                        minlength: 3,
                        alphanumeric: true
                    },
                    phone_number: {
                        required: true,
                        //number: true,
                        indianMobile: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    }
                },
                onfocusout: function(element) {
                    this.element(element); // triggers validation
                },
                onkeyup: function(element, event) {
                    this.element(element); // triggers validation
                },
                messages : {
                    
                    phone_number: {
                        required: "{{ __('Please enter your phone')}}",
                        number: "{{ __('Please enter a numerical value')}}"
                    },
               
                }
            });

            $("#register").submit(function() {
                if($("#phone").hasClass("is-invalid")){
                    $("#phone").focus();
                    return false;
                }
            });
        });
        jQuery(window.document).ready(function () {
            jQuery("body").addClass("register_body");
        });
        jQuery(document).ready(function($) {
            setTimeout(function(){
                var footer_height = $('.footer-light').height();
                console.log(footer_height);
                $('article#content-wrap').css('padding-bottom',footer_height);
            }, 500);
            setTimeout(function(){
                $("#phone").val({{ old('phone_number') }});
            }, 2500);
        });
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
            separateDialCode: true,
            hiddenInput: "full_number",
            utilsScript: "{{ asset('assets/js/utils.js') }}",
            initialCountry: "{{ Session::get('default_country_code', 'US') }}",
        });
        phoneNumbervalidation(iti, input);
        $(document).ready(function() {
            $("#phone").keypress(function(e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
                }
                return true;
            });
        });
        $('.iti__country').click(function() {
            var code = $(this).attr('data-country-code');
            $('#countryData').val(code);
            var dial_code = $(this).attr('data-dial-code');
            $('#dialCode').val(dial_code);
        });
        $(document).on('change', '[id^=input_file_logo_]', function(event) {
            var rel = $(this).data('rel');
            // $('#plus_icon_'+rel).hide();
            readURL(this, '#upload_logo_preview_' + rel);
        });

        function getExtension(filename) {
            return filename.split('.').pop().toLowerCase();
        }

        function readURL(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var extension = getExtension(input.files[0].name);
                reader.onload = function(e) {
                    if (extension == 'pdf') {
                        $(previewId).attr('src', "{{ asset('assets/images/pdf-icon-png-2072.png') }}");
                    } else if (extension == 'csv') {
                        $(previewId).attr('src', text_image);
                    } else if (extension == 'txt') {
                        $(previewId).attr('src', text_image);
                    } else if (extension == 'xls') {
                        $(previewId).attr('src', text_image);
                    } else if (extension == 'xlsx') {
                        $(previewId).attr('src', text_image);
                    } else {
                        $(previewId).attr('src', e.target.result);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
				jQuery('.submitVerifyuser').click(function(e){
		        let formData = $('#verifyuserphone').serializeArray();
				e.preventDefault();
				$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json",
				},
				url: "{{ route('customer.verifyPhoneNo') }}",
				data: formData,
				success: function (response) {
					if(response.status == 'error')
				{
					$(".invalid-feedback").html(response.message);
				}
				else{
					$('.sendOtpform').addClass('d-none');
					$('.verifyotpform').removeClass('d-none');
					console.log(response.dialCode);
					$('#phone2').val(response.request_data.phone_number);
					alert(response.request_data.dialCode);
					$('#dialCode2').val(response.request_data.dialCode);
					$('#countryData').val(response.request_data.countryData);
					iti.setCountry(response.request_data.countryData);
				  }
				},
				error: function (response) {
					$(".show_all_error.invalid-feedback").show();
						$(".show_all_error.invalid-feedback").text(
							"Something went wrong, Please try Again."
						);
				},
			});
		  
		});
		$("#verify_phone_token").click(function(event) {
		event.preventDefault();
        var verifyToken = '';
        $('.digit-group').find('input').each(function() {
            if ($(this).val()) {
                verifyToken += $(this).val();
            }
        });
        var form_inputs = $("#verifyuserOtp").serializeArray();
        form_inputs.push({
            name: 'verifyToken',
            value: verifyToken
        });
		console.log('22222222222222222');
		console.log(form_inputs);
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('customer.verifyPhoneLoginOtpCustom') }}",
            data: form_inputs,
            success: function(response) {
                if (response.status == 'Success') {
                   // window.location.reload();
                } else {
				    $(".invalid_phone_otp_error").html(response.message);
                    setTimeout(function() {
                        $('.invalid_phone_otp_error').html('').hide();
                    }, 5000);
                }
            },
            error: function(data) {
				alert();
				console.log(data);
                $(".invalid_phone_otp_error").html(data.responseJSON.message);
                
				setTimeout(function() {
                    $('.invalid_phone_otp_error').html('').hide();
                }, 5000);


            },
        });
    });


	function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    $('.digit-group').find('input').each(function() {
        $(this).attr('maxlength', 1);
        $(this).on('keyup', function(e) {
            var parent = $($(this).parent());
            if(e.keyCode === 8 || e.keyCode === 37) {
                var prev = parent.find('input#' + $(this).data('previous'));
                if(prev.length) {
                    $(prev).select();
                }
            } else if((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
                var next = parent.find('input#' + $(this).data('next'));
                if(next.length) {
                    $(next).select();
                } else {
                    if(parent.data('autosubmit')) {
                        parent.submit();
                    }
                }
            }
        });
    });
	$('#back_send_otp').click(function(){
		$('.verifyotpform').addClass('d-none');
		$('.sendOtpform').removeClass('d-none');

	});
    </script>
@endsection

<script>
		window.onload = function() {
            $('#phoneVerificationModal').modal('show');
        };
</script>

@endif

<section class="section-b-space_  p-0 ratio_asos banner_shimmer">
	<div class="container-fulid shimmer_effect  main_shimer topBar">
        <div class="row">
            <div class="col-12 cards">
                <div class="logoArea_bar loading"></div>
            </div>
        </div>
    </div>
	<div class="container shimmer_effect main_shimer">
		<div class="row">
			<div class="col-12 cards">
				<div class="cardbanner loading"></div>
			</div>
		</div>
	</div>
	<div class="container mb-md-5 shimmer_effect main_shimer">
		<div class="row">
			<div class="col-12 cards">
				<h2 class="h2-heading loading mb-3"></h2>
			</div>
		</div>
        <div class="row">

            <div class="col-sm-12">
                <div class="grid-row grid-4-4">
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
					<div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                </div>
            </div>
        </div>

	</div>
	<div class="container mb-md-5 shimmer_effect main_shimer">
		<div class="row">
			<div class="col-12 cards">
				<h2 class="h2-heading loading mb-3"></h2>
			</div>
		</div>
        <div class="row">

            <div class="col-sm-12">
                <div class="grid-row grid-4-4">
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-sm-1 grid-row px-sm-3 p-0 d-sm-block d-none">
                <div class="card_image loading"></div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="card_title loading"></div>
                    <div class="card_icon loading"></div>
                </div>
                <div class="card_content loading mt-0 w-75"></div>
                <div class="card_content loading mt-0 w-50"></div>
                <div class="card_line loading"></div>
                <div class="card_price loading"></div>
            </div> -->
        </div>

	</div>
	<div class="container mb-md-5 shimmer_effect main_shimer">
		<div class="row">
			<div class="col-12 cards">
				<h2 class="h2-heading loading mb-3"></h2>
			</div>
		</div>
        <div class="row">

            <div class="col-sm-12">
                <div class="grid-row grid-4-4">
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                    <div class="cards">
                        <div class="card_image loading"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card_title loading"></div>
                            <div class="card_icon loading"></div>
                        </div>
                        <div class="card_content loading mt-0 w-75"></div>
                        <div class="card_content loading mt-0 w-50"></div>
                        <div class="card_line loading"></div>
                        <div class="card_price loading"></div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-sm-1 grid-row px-sm-3 p-0 d-sm-block d-none">
                <div class="card_image loading"></div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="card_title loading"></div>
                    <div class="card_icon loading"></div>
                </div>
                <div class="card_content loading mt-0 w-75"></div>
                <div class="card_content loading mt-0 w-50"></div>
                <div class="card_line loading"></div>
                <div class="card_price loading"></div>
            </div> -->
        </div>

	</div>
</section>

 <!-- Shimmer Efferct End -->

<!-- html code here -->
<button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#login_modal"> Launch demo modal </button>
@if(count($banners))
<section class="home-slider-wrapper pt-md-3 pb-0">

	<div class="container">
		<div id="myCarousel" class="carousel slide al_desktop_banner" data-ride="carousel">
			<div class="carousel-inner">
				@foreach($banners as $key => $banner)
					@php $url=''; if($banner->link=='category'){if(!empty($banner->category_slug)){$url=route('categoryDetail', $banner->category_slug);}}else if($banner->link=='vendor'){if(!empty($banner->vendor_slug)){$url=route('vendorDetail', $banner->vendor_slug);}}else if($banner->link=='url'){if($banner->link_url !=null){$url=$banner->link_url;}}@endphp
					<div class="carousel-item @if($key == 0) active @endif">
					 <a class="banner-img-outer" href="{{$url??'#'}}" target="_blank">
                        <link rel="preload" as="image" href="{{ get_file_path($banner->image,'IMG_URL1','1370','300') }}" />
						<img alt="" title="" class="blur-up lazyload w-100" data-src="{{ get_file_path($banner->image,'IMG_URL1','1370','300') }}">
					</a>
					</div>
				@endforeach

			</div>
			<a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">{{__('Previous')}}</span>
			</a>
			<a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">{{__('Next')}}</span>
			</a>
		</div>

		<div id="myMobileCarousel" class="carousel slide al_mobile_banner mb-2" data-ride="carousel" style="display:none;">
			<div class="carousel-inner">

				@foreach($mobile_banners as $key => $banner)
					@php $url=''; if($banner->link=='category'){if(!empty($banner->category_slug)){$url=route('categoryDetail', $banner->category_slug);}}else if($banner->link=='vendor'){if(!empty($banner->vendor_slug)){$url=route('vendorDetail', $banner->vendor_slug);}}@endphp
					<div class="carousel-item @if($key == 0) active @endif">
					 <a class="banner-img-outer" href="{{$url??'#'}}">
                        <link rel="preload" as="image" href="{{ get_file_path($banner->image,'IMG_URL1','400','150') }}" />
						<img alt="" title="" class="blur-up lazyload w-100" data-src="{{ get_file_path($banner->image,'IMG_URL1','400','150') }}">
					</a>
					</div>
				@endforeach

			</div>
			<a class="carousel-control-prev" href="#myMobileCarousel" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">{{__('Previous')}}</span>
			</a>
			<a class="carousel-control-next" href="#myMobileCarousel" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">{{__('Next')}}</span>
			</a>
		</div>

	</div>
</section>
@else
<section class="home-slider-wrapper">
	<div class="container-fulid">
		<div id="myCarousel" class="carousel slide al_desktop_banner" data-ride="carousel"></div>
		<div id="myMobileCarousel" class="carousel slide al_mobile_banner mb-2" data-ride="carousel" style="display:none;"></div>
	</div>
</section>
@endif



<!-- no-store-wrapper start -->
<section class="no-store-wrapper mb-3 mt-3" style="display: none;" >
	<div class="container">
        @if(count($for_no_product_found_html))
            @foreach($for_no_product_found_html as $key => $homePageLabel)
                @include('frontend.included_files.dynamic_page')
            @endforeach
        @else
		<div class="row">
			<div class="col-12 text-center"> <img class="no-store-image mt-2 mb-2 blur-up lazyload" data-src="{{getImageUrl(asset('images/no-stores.svg'),'250/250')}}" style="max-height: 250px;"> </div>
		</div>
		<div class="row">
			<div class="col-12 text-center mt-2">
				<h4>{{__('We are currently not operating in your location.')}}</h4> </div>
		</div>
        @endif
    </div>
</section><!-- no-store-wrapper end -->

<script type="text/template" id="desktop_banners_template">
	<div class="carousel-inner">
	   <% _.each(banners, function(banner, k){%>
		  <%
		  var url='#';
		  if(banner.link == 'category'){
			 if(banner.category != null){
				url = "{{route('categoryDetail')}}" + "/" + banner.category.slug;
			 }
		  }
          else if(banner.link == 'vendor'){
			 if(banner.vendor != null){
				url = "{{route('vendorDetail')}}" + "/" + banner.vendor.slug;
			 }
		  }
		  %>
		  <div class="carousel-item <% if(k == 0) { %> active <% } %>">
			 <a class="banner-img-outer" href="<%= url %>">
				<link rel="preload" as="image" href="<%= banner.image.proxy_url %>1370/300<%= banner.image.image_path %>" />
				<img alt="" title="" class="blur-up lazyload w-100" data-src="<%= banner.image.proxy_url %>1370/300<%= banner.image.image_path %>">
			 </a>
		  </div>
	   <% }); %>
	</div>
	<a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">{{__('Previous')}}</span>
	</a>
	<a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">{{__('Next')}}</span>
	</a>
</script>

<script type="text/template" id="mobile_banners_template">
	<div class="carousel-inner">
	   <% _.each(banners, function(banner, k){%>
		  <%
		  var url='#';
		  if(banner.link == 'category'){
			 if(banner.category != null){
				url = "{{route('categoryDetail')}}" + "/" + banner.category.slug;
			 }
		  }
          else if(banner.link == 'vendor'){
			 if(banner.vendor != null){
				url = "{{route('vendorDetail')}}" + "/" + banner.vendor.slug;
			 }
		  }
		  %>
		  <div class="carousel-item <% if(k == 0) { %> active <% } %>">
			 <a class="banner-img-outer" href="<%= url %>">
				<link rel="preload" as="image" href="<%= banner.image.proxy_url %>400/150<%= banner.image.image_path %>" />
				<img alt="" title="" class="blur-up lazyload w-100" data-src="<%= banner.image.proxy_url %>400/150<%= banner.image.image_path %>">
			 </a>
		  </div>
	   <% }); %>
	</div>
	<a class="carousel-control-prev" href="#myMobileCarousel" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">{{__('Previous')}}</span>
	</a>
	<a class="carousel-control-next" href="#myMobileCarousel" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">{{__('Next')}}</span>
	</a>
</script>


 <!-- vendors_template start -->
<script type="text/template" id="vendors_template" >

	<% _.each(vendors, function(vendor, k){%>
		<div class="product-card-box position-relative text-center al_custom_vendors_sec"  >
			<a class="suppliers-box d-block" href="{{route('vendorDetail')}}/<%=vendor.slug %>">
				<div class="suppliers-img-outer position-relative ">
					<% if(vendor.is_vendor_closed==1){%>
						<img class="fluid-img mx-auto blur-up lazyload grayscale-image" data-src="<%=vendor.logo.image_fit %>200/200<%=vendor.logo['image_path'] %>" alt="" title="">
					<%}else{%>
						<img  class="fluid-img mx-auto blur-up lazyload" data-src="<%=vendor.logo.image_fit %>200/200<%=vendor.logo['image_path'] %>" alt="" title="">
					<%}%>

				</div>
				<div class="supplier-rating">
					<h6 class="mb-1 ellips"><%=vendor.name %></h6>
					{{--<p title="<%=vendor.categoriesList %>" class="vendor-cate mb-1 ellips d-none">
						<%=vendor.categoriesList %>
					</p>--}}
						<% if(vendor.timeofLineOfSightDistance !=undefined){%>
							<div class="pref-timing"> <span><%=vendor.timeofLineOfSightDistance %></span> </div>
						<%}%>
				</div>
				@if($client_preference_detail) @if($client_preference_detail->rating_check==1)
				<% if(vendor.vendorRating > 0){%> <span class="rating-number"><%=vendor.vendorRating %> </span>
				<%}%> @endif @endif
			</a>
		</div>
		<% }); %>
</script><!-- vendors_template end -->

<!-- banner_template start -->
<script type="text/template" id="banner_template" >
	<% _.each(brands, function(brand, k){%>
		<div  >
			<a class="brand-box d-block black-box" href="<%=brand.redirect_url %>">
				<div class="brand-ing"> <img class="blur-up lazyload" data-src="<%=brand.image.image_fit %>260/260<%=brand.image.image_path %>" alt="" title=""> </div>
				<h6><%=brand.translation_title %></h6> </a>
		</div>
		<% }); %>
</script><!-- banner_template end -->

<!-- products_template start -->
<script type="text/template" id="products_template" >
	<% _.each(products, function(product, k){ %>
		<div class="product-card-box position-relative al_box_third_template al"  >
			{{--<div class="add-to-fav 12">
				<input id="fav_pro_one" type="checkbox">
				<label for="fav_pro_one"><i class="fa fa-heart-o fav-heart" aria-hidden="true"></i></label>
			</div>--}}
			<a class="common-product-box text-center" href="<%=product.vendor.slug %>/product/<%=product.url_slug %>">
				<div class="img-outer-box position-relative"> <img class="blur-up lazyload" data-src="<%=product.image_url %>" alt="" title="">
					<div class="pref-timing"> </div>
				</div>
				<div class="media-body align-self-start">
					<div class="inner_spacing px-0">
						<div class="product-description">
							<div class="d-flex align-items-center justify-content-between">
								<h6 class="card_title ellips"><%=product.title %></h6> @if($client_preference_detail) @if($client_preference_detail->rating_check==1)
								<% if(product.averageRating > 0){%> <span class="rating-number"><%=product.averageRating %></span>
									<%}%> @endif @endif </div>
							<div class="product-description_list border-bottom">
								<p>
									<%=product.vendor_name %>
								</p>
								<p class="al_product_category">
									<span>
								{{__('In')}}
									<%=product.category %></span>
								</p>
							</div>
							<div class="d-flex align-items-center justify-content-between al_clock pt-2">
								<b><% if(product.inquiry_only==0){%> <%=product.price %> <%}%></b>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		<% }); %>
</script><!-- products_template end -->

<!-- trending_vendors_template start -->
<script type="text/template" id="trending_vendors_template" >
	<% _.each(trending_vendors, function(vendor, k){%>
		<div class="product-card-box position-relative text-center al_custom_vendors_sec"  >
			<a class="suppliers-box al_vendors_template2 d-block" href="{{route('vendorDetail')}}/<%=vendor.slug %>">
				<div class="suppliers-img-outer position-relative ">
					<% if(vendor.is_vendor_closed==1){%> <img class="fluid-img mx-auto blur-up lazyload grayscale-image" data-src="<%=vendor.logo.image_fit %>200/200<%=vendor.logo['image_path'] %>" alt="" title="">
						<%}else{%> <img class="fluid-img mx-auto blur-up lazyload w-100" data-src="<%=vendor.logo.image_fit %>200/200<%=vendor.logo['image_path'] %>" alt="" title="">
							<%}%>
				</div>
				<div class="supplier-rating">
					<h6 class="mb-1 ellips"><%=vendor.name %></h6>
					<p title="<%=vendor.categoriesList %>" class="vendor-cate mb-1 ellips d-none">
						<%=vendor.categoriesList %>
					</p>

						<% if(vendor.timeofLineOfSightDistance !=undefined){%>
									<div class="pref-timing"> <span><%=vendor.timeofLineOfSightDistance %></span> </div>
									<%}%>
				</div>
				@if($client_preference_detail) @if($client_preference_detail->rating_check==1)
						<% if(vendor.vendorRating > 0){%>
						<span class="rating-number"><%=vendor.vendorRating %> </span>
						<%}%> @endif @endif
			</a>
		</div>
		<% }); %>
</script><!-- trending_vendors_template end -->

<!-- recent_orders_template start -->
<script type="text/template" id="recent_orders_template"  >
	<% _.each(recent_orders, function(order, k){ %>
		<% subtotal_order_price = total_order_price = total_tax_order_price = 0; %>
			<% _.each(order.vendors, function(vendor, k){ %>
				<%   product_total_count = product_subtotal_amount = product_taxable_amount = 0; %>
				@include('frontend.common_section.recent_order_j')
					<% }); %>
						<% }); %>
</script><!-- recent_orders_template end -->

<script type="text/template" id="cities_template" >
<% _.each(cities, function(city, k){%>
	<div class="alSpaListSlider">
		<div>
			<div class="alSpaListBox">
			<div class="alSpaCityBox">
				<a href="/cities/<%=city.slug %>"><img class="w-100" src="<%=city.image.image_fit %>260/260<%=city.image.image_path %>"></a>
			</div>
			<p><%=city.title %></p>
			</div>
		</div>
	</div>
	<% });
%>
</script><!-- cities cities end -->

<!-- our_vendor_main_div start -->
<section class="section-b-space ratio_asos pt-0 mt-0 pb-0 {{isset($client_preference_detail) && $client_preference_detail->business_type == 'taxi' ? 'taxi' : ''}}" id="our_vendor_main_div" >

	<div class="vendors"> @foreach($homePageLabels as $key => $homePageLabel)
		@if($homePageLabel->slug == 'pickup_delivery')
		@if(isset($homePageLabel->pickupCategories) && count($homePageLabel->pickupCategories)) @include('frontend.booking.cabbooking-single-module') @endif
		@elseif($homePageLabel->slug == 'dynamic_page') @include('frontend.included_files.dynamic_page')
		@elseif($homePageLabel->slug == 'brands' && (count($homePageData['brands']) != 0))
			<section class="container popular-brands left-shape_ position-relative ">
				<div class="al_top_heading d-flex justify-content-between">
					<h2 class="h2-heading">{{(!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : getNomenclatureName('brands', true)}}</h2>
						{{-- <a class="" href="">See All</a> --}}
				</div>
				<div class="row">
					<div class=" col-12 al_custom_brand">
					<div class=" brand-slider render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
                        @foreach ($homePageData[$homePageLabel->slug] as $brand )
						@include('frontend.home_page_2.brands')
                        @endforeach
					</div>
					</div>
				</div>
			</section>
		@elseif($homePageLabel->slug == 'cities' && (count($homePageData['cities']) != 0))
			<section class="suppliers-section container render_full_{{$homePageLabel->slug}}">
				<div class=" top-heading d-flex justify-content-between align-self-center">
					<h2 class="h2-heading">{{(!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : 'Cities'}}</h2>
				</div>
				<div class="col-12">
					<div class="suppliers-slider-{{$homePageLabel->slug}} product-m render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
						@foreach ($homePageData[$homePageLabel->slug] as $cities )
                        <div class="alSpaListSlider">
                           <div>
                              <div class="alSpaListBox">
                                 <div class="alSpaCityBox">
                                    <a href="javascript:void(0);" class="cities updateLocationByCity" data-lat="{{$cities['latitude']}}" data-long="{{$cities['longitude']}}" data-place_id="{{$cities['place_id']}}" data-address="{{$cities['address']}}"><img class="w-100" src="{{$cities['image']['image_fit']}}260/260{{$cities['image']['image_path']}}"></a>
                                 </div>
                                 <p>{{$cities["title"]}} </p>
                              </div>
                           </div>
                        </div>
                        @endforeach
					</div>
				</div>
			</section>
		@elseif($homePageLabel->slug == 'vendors' && (count($homePageData['vendors']) != 0))
			<section class="suppliers-section container ">
				<div class=" top-heading d-flex justify-content-between align-self-center">
					<h2 class="h2-heading">{{(!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : getNomenclatureName('vendors', true)}}</h2>
					<a class="" href="{{route('vendor.all')}}">{{__("See all")}}</a>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="suppliers-slider-{{$homePageLabel->slug}} product-m render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
							@foreach ($homePageData[$homePageLabel->slug] as $vendor )
							@include('frontend.home_page_3.vendor')
							@endforeach
						</div>
					</div>
				</div>
			</section>
		@elseif($homePageLabel->slug == 'trending_vendors' && (count($homePageData['trending_vendors']) != 0))
			<section class="suppliers-section container" id="homepage_trending_vendors_div">
				<div class=" top-heading ">
					<h2 class="h2-heading">{{$homePageLabel->slug=='trending_vendors' ? __('Trending')." ".getNomenclatureName('vendors', true) : __($homePageLabel->title)}}</h2> </div>
				<div class="row">
						<div class="col-12 p-0">
							<div class="suppliers-slider-{{$homePageLabel->slug}} product-m render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
								@foreach ($homePageData[$homePageLabel->slug] as $vendor )
								@include('frontend.home_page_3.vendor')
								@endforeach
							</div>
						</div>
				</div>
			</section>
        @elseif($homePageLabel->slug == 'long_term_service' && (count($homePageData['long_term_service']) != 0))
        <section class="suppliers-section container" id="homepage_long_term_service_div">
            <div class=" top-heading ">
                <h2 class="h2-heading">{{$homePageLabel->slug =='long_term_service' ? __('Long Term')." ".getNomenclatureName('service', true) : __($homePageLabel->title)}}</h2>
            </div>
            <div class="row">
                <div class="col-12 p-0">
					{{-- @php
					pr($homePageData[$homePageLabel->slug]);
					@endphp --}}
                    <div class="suppliers-slider-{{$homePageLabel->slug}} product-m render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
                        @foreach ($homePageData[$homePageLabel->slug] as $value )
					
                        @include('frontend.home_page_3.long_term_service')
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
					
	   @elseif($homePageLabel->slug == 'recent_orders' && (@count($homePageData['recent_orders']) != 0 ))
			<section class="container mb-0 render_full_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}"  >
				<div class="top-heading d-flex justify-content-between">
					<h2 class="h2-heading"> @php
						echo (!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : __("Your Recent Orders");
					@endphp </h2>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="recent-orders product-m  render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
							@foreach ($homePageData[$homePageLabel->slug] as $order )
						
							@endforeach
						</div>
					</div>
				</div>
			</section>
		@elseif($homePageLabel->slug == 'best_sellers' && (count($homePageData['best_sellers']) != 0))
			<section class="container mb-0 render_full_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}"  >
				<div class="top-heading d-flex justify-content-between">
					<h2 class="h2-heading"> @php
						echo (!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : __($homePageLabel->title);
					@endphp </h2>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="product-4-{{$homePageLabel->slug}} product-m  render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
							@foreach ($homePageData[$homePageLabel->slug] as $vendor )
							@include('frontend.home_page_3.vendor')
							@endforeach
						</div>
					</div>
				</div>
			</section>
		@elseif($homePageLabel->slug == 'banner' && (count($homePageData['banners']) != 0))
			@if(!empty(@$homePageData['banners'][$homePageLabel->translations->first()->cab_booking_layout_id]))
				<section class="container mb-0 render_full_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}"  >
					<div class="top-heading d-flex justify-content-between">
						<h2 class="h2-heading"> @php
							echo (!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : __($homePageLabel->title);
						@endphp </h2>
					</div>

					<div class="custom_banner">
						<div class="container">
							<div class="text-center">
								@php
								    $url = $homePageData['banners'][$homePageLabel->translations->first()->cab_booking_layout_id]; // replace with your URL
									$extension = pathinfo($url, PATHINFO_EXTENSION);
									$image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']; // list of image extensions
									$video_extensions = ['mp4', 'avi', 'mov', 'wmv']; // list of video extensions
								@endphp
								@if(in_array($extension, $image_extensions))
									<img alt="" title="" class="blur-up lazyload w-100" src="{{$homePageData['banners'][$homePageLabel->translations->first()->cab_booking_layout_id]}}" height="300">	
								@elseif (in_array($extension, $video_extensions))
									<video id="video1" width="100%" controls autoplay muted>
										<source src="{{$homePageData['banners'][$homePageLabel->translations->first()->cab_booking_layout_id]}}" type="video/mp4">
									</video>
								@else
								@endif
							</div>
						</div>
					</div>
				</section>
			@endif
		@else
			@if(!empty(@$homePageData[$homePageLabel->slug]) && @count(@$homePageData[$homePageLabel->slug]) != 0)
				<section class="container mb-0 render_full_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}"  >
						<div class="top-heading d-flex justify-content-between">
							<h2 class="h2-heading"> @php
								echo (!empty($homePageLabel->translations->first()->title)) ? $homePageLabel->translations->first()->title : __($homePageLabel->title);
							@endphp </h2>
						</div>
					<div class="row">
						<div class="col-12">
							<div class="product-4-{{$homePageLabel->slug}} product-m  render_{{$homePageLabel->slug}}" id="{{$homePageLabel->slug.$key}}">
								@foreach ($homePageData[$homePageLabel->slug] as $product )
								@include('frontend.home_page_3.product')
								@endforeach
							</div>
						</div>
					</div>
				</section>
			@endif
		@endif @endforeach
	</div>
</section><!-- our_vendor_main_div end -->



<!-- age-restriction star -->
<div class="modal age-restriction fade" id="age_restriction" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body text-center"> <img style="height: 150px;" class="blur-up lazyload" data-src="{{getImageUrl(asset('assets/images/age-img.svg'),'150/150')}}" alt="" title="">
				<p class="mb-0 mt-3">{{$client_preference_detail ? $client_preference_detail->age_restriction_title : __('Are you 18 or older?')}}</p>
				<p class="mb-0">{{__('Are you sure you want to continue?')}}</p>
			</div>
			<div class="modal-footer d-block">
				<div class="row no-gutters">
					<div class="col-6 pr-1">
						<button type="button" class="btn btn-solid w-100 age_restriction_yes" data-dismiss="modal">{{__('Yes')}}</button>
					</div>
					<div class="col-6 pl-1">
						<button type="button" class="btn btn-solid w-100 age_restriction_no" data-dismiss="modal">{{__('No')}}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!-- age-restriction end -->

<!-- footer code in layouts.store/footercontent-template-two -->
@section('home-page')
{{-- <script type="text/javascript" src="{{asset('front-assets/js/homepage-three.js')}}"></script> --}}
<script type="text/javascript" src="{{asset('assets/js/template/commonFunction.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/template/template-three/templateFunction.js')}}"></script>
<script>
	var featured_products_length = {{ isset($homePageData['featured_products']) ? count($homePageData['featured_products']) : ''}};
</script>
@endsection
@endsection
@section('js-script')
{{--<script type="text/javascript" src="{{asset('front-assets/js/jquery.exitintent.js')}}"></script>
<script type="text/javascript" src="{{asset('front-assets/js/fly-cart.js')}}"></script>
<script type="text/javascript" src="{{asset('js/aos.js')}}"></script>--}}
@endsection
@section('script')
@endsection
