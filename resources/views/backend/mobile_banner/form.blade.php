<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12" id="imageInput">
                @if(isset($banner->id))


                <label>{{ __("Upload Mobile banner image") }}</label>
                <input type="file" accept="image/*" data-plugins="dropify" name="image" class="dropify" data-default-file="{{$banner->image['proxy_url'].'1900/500'.$banner->image['image_path']}}" />



                @else

                <label>{{ __("Upload Mobile banner image") }}</label>
                <input data-default-file="" type="file" data-plugins="dropify" name="image" accept="image/*" class="dropify" />


                @endif
                <label class="logo-size text-right w-100">{{ __("Logo Size") }} 1920x550</label>

                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group" id="nameInput">
                    {!! Form::label('title', __('Name'),['class' => 'control-label']) !!}
                    {!! Form::text('name', $banner->name, ['class' => 'form-control']) !!}
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('title', __('Enable'),['class' => 'control-label']) !!}
                    <div>
                        <?php $validity = (isset($banner->id) && $banner->id > 0) ? 'validity_edit' : 'validity_add'; ?>
                        @if((isset($banner) && $banner->validity_on == '0'))
                        <input type="checkbox" data-plugin="switchery" name="validity_on" class="form-control {{$validity}}" data-color="#43bee1">
                        @else
                        <input type="checkbox" data-plugin="switchery" name="validity_on" class="form-control {{$validity}}" data-color="#43bee1" checked='checked'>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group" id="start_date_timeInput">
                    @php
                    $minDate = Date('Y-m-d');
                    @endphp
                    {!! Form::label('title', __('Start Date'),['class' => 'control-label']) !!}
                    {!! Form::text('start_date_time', $banner->start_date_time, ['class' => 'form-control downside datetime-datepicker', 'id' => 'start-datepicker', 'min' => $minDate]) !!}

                    <span class="invalid-feedback" role="alert">
                        <input type="hidden" name="banner_id" value="{{isset($banner->id) ? $banner->id : ''}}">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-6">

                <div class="form-group" id="end_date_timeInput">
                    {!! Form::label('title', __('End Date'),['class' => 'control-label']) !!}
                    {!! Form::text('end_date_time', $banner->end_date_time, ['class' => 'form-control downside datetime-datepicker', 'id' => 'end-datepicker' ]) !!}
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('title', __('Assign To'),['class' => 'control-label']) !!}
                    <select class="selectize-select form-control assignToSelect" name="assignTo">
                        <option value="">{{ __("Select") }}</option>
                        <option value="category" {{($banner->link == 'category') ? 'selected' : ''}}>{{ __('Category') }}</option>
                        @if($client_preference_detail->business_type != 'taxi')
                        <option value="vendor" {{($banner->link == 'vendor') ? 'selected' : ''}}>{{ __("Vendor") }}</option>
                        @endif
                        <option value="url" {{($banner->link == 'url') ? 'selected' : ''}}>{{ __("URL Link") }}</option>
                    </select>

                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="row category_vendor" style="{{(empty($banner->link)) ? 'display: none;' : ''}}">
            <div class="col-md-6 category_list" style="{{($banner->link == 'category') ? '' : 'display: none;'}}">
                <input type="hidden" id="bannerId" url="{{ (isset($banner->id) && $banner->id > 0) ? route('mobilebanner.update', $banner->id) : route('mobilebanner.store') }}">
                <div class="form-group">
                    {!! Form::label('title', __('Select Category'),['class' => 'control-label']) !!}
                    <select class="selectize-select form-control" name="category_id">
                        <option value="">Select</option>
                        @foreach($categories as $key => $cate)
                        <option value="{{$cate->id}}" {{($cate->id == $banner->redirect_category_id) ? 'selected' : ''}}>{{$cate->translation_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 vendor_list" style="{{($banner->link == 'vendor') ? '' : 'display: none;'}}">
                <div class="form-group">
                    {!! Form::label('title', __('Select Vendor'),['class' => 'control-label']) !!}
                    <select class="selectize-select form-control" name="vendor_id">
                        <option value="">Select</option>
                        @foreach($vendors as $key => $vend)
                        <option value="{{$vend->id}}" {{($vend->id == $banner->redirect_vendor_id) ? 'selected' : ''}}>{{$vend->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 url_link" style="{{($banner->link == 'url') ? '' : 'display: none;'}}">
                <div class="form-group">
                    {!! Form::label('title', __('Enter Link'),['class' => 'control-label']) !!}
                    <input type="url" id="link_url" name="link_url" value="{{$banner->link_url ?? ''}}" class="form-control">
                </div>
            </div>
        </div>
        @if(isset($client_preferences->is_service_area_for_banners) && ($client_preferences->is_service_area_for_banners == 1) && ($client_preferences->is_hyperlocal == 1))
        <div class="row">
            <div class="col-md-12" >
                <div class="form-group">
                    <label class="control-label">{{ __("Service Area") }}</label>
                    <select class="form-control select2-multiple" name="banner_service_area[]" data-toggle="select2" multiple="multiple" placeholder="Select Service Area...">
                        @foreach($areas as $area)
                            <option value="{{$area->id}}" {{ (in_array($area->id, $selected_areas)) ? 'selected' : '' }}>{{$area->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>