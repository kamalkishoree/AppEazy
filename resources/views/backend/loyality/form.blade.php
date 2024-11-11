<div class="row mb-2">
  <div class="col-md-12">
    <label>{{ __('Upload Image') }} </label>
    <input type="file" accept="image/*" data-plugins="dropify" name="image" class="dropify" data-default-file="{{ $lc->image['proxy_url'].'100/100'.$lc->image['image_path'] }}" />
  </div>
</div>
<div class="row mb-2">
  <div class="col-md-6">
    <div class="form-group" id="nameInput"> {!! Form::label('title', __('Name'),['class' => 'control-label']) !!} {!! Form::text('name', $lc->name, ['class' => 'form-control', 'placeholder' => 'Name']) !!} <span class="invalid-feedback" role="alert">
        <strong></strong>
      </span>
    </div>
  </div>


  <div class="col-md-12">
                                    <div class="form-group" id="titleInput">
                                        <h5> {!! Form::label('title', __('Name'),['class' => 'control-label']) !!} </h5>
                                        <table class="table table-borderless table-responsive al_table_responsive_data" id="banner-datatable" >
                                            <tr >
                                                @foreach($languages as $langs)
                                                @if($langs->language->name == "English")
                                                    @continue;
                                                @endif
                                                    <th>{{$langs->language->name}}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($languages as $langs)
                                                @if($langs->language->name == "English")
                                                    @continue;
                                                @endif
                                                    @if($langs->is_primary == 1)
                                                        <td >
                                                            <!-- {!! Form::hidden('name_language_id[]', $langs->language_id) !!} -->
                                                            {!! Form::text('name_translation['.$langs->language_id.']', @$loyaltyCardNames[$langs->language_id], ['class' => 'form-control', 'required' => 'required']) !!}
                                                        </td>
                                                    @else
                                                        <td >
                                                            <!-- {!! Form::hidden('name_language_id[]', $langs->language_id) !!} -->
                                                            {!! Form::text('name_translation['.$langs->language_id.']', @$loyaltyCardNames[$langs->language_id], ['class' => 'form-control']) !!}
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </table>
                                        {{-- {!! Form::text('title','', ['class' => 'form-control', 'placeholder'=>'Enter Title']) !!} --}}
                                        <span class="invalid-feedback" role="alert">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>




  <div class="col-md-6">
    <div class="form-group" id="minimum_pointsInput"> {!! Form::label('title', __('Min. Points to reach this level *'),['class' => 'control-label']) !!} {!! Form::text('minimum_points', $lc->minimum_points, ['class' => 'form-control']) !!} <span class="invalid-feedback" role="alert">
        <strong></strong>
      </span>
    </div>
  </div>
  <div class="col-md-12">
    <div class="form-group" id="descriptionInput"> {!! Form::label('title', __('Description *'),['class' => 'control-label']) !!} {!! Form::textarea('description', $lc->description, ['class' => 'form-control', 'rows' => '3']) !!} <span class="invalid-feedback" role="alert">
        <strong></strong>
      </span>
    </div>
  </div>
                  <div class="col-md-12">
                                    <div class="form-group" id="titleInput">
                                        <h5> {!! Form::label('title', __('Description'),['class' => 'control-label']) !!} </h5>
                                        <table class="table table-borderless table-responsive al_table_responsive_data" id="banner-datatable" >
                                            <tr >
                                                @foreach($languages as $langs)
                                                @if($langs->language->name == "English")
                                                    @continue;
                                                @endif
                                                    <th>{{$langs->language->name}}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($languages as $langs)
                                                @if($langs->language->name == "English")
                                                    @continue;
                                                @endif
                                                    @if($langs->is_primary == 1)
                                                        <td >
                                                            <!-- {!! Form::hidden('description_language_id[]', $langs->language_id) !!} -->
                                                            {!! Form::text('description_translation['.$langs->language_id.']', @$loyaltyCardDesc[$langs->language_id], ['class' => 'form-control', 'required' => 'required']) !!}
                                                        </td>
                                                    @else
                                                        <td >
                                                            <!-- {!! Form::hidden('description_language_id[]', $langs->language_id) !!} -->
                                                            {!! Form::text('description_translation['.$langs->language_id.']', @$loyaltyCardDesc[$langs->language_id], ['class' => 'form-control']) !!}
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </table>
                                        {{-- {!! Form::text('title','', ['class' => 'form-control', 'placeholder'=>'Enter Title']) !!} --}}
                                        <span class="invalid-feedback" role="alert">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
</div>
<div class="bg-light mt-3 p-2 border-radius">
    <h5>Earnings</h5>
    <input type="hidden" name="lc_id" id="lc_id" url="{{route('loyalty.update', $lc->id)}}">
    <div class="form-group" id="per_order_pointsInput"> {!! Form::label('title', __('Earnings Per Order*'),['class' => 'control-label']) !!} {!! Form::text('per_order_points', $lc->per_order_points, ['class' => 'form-control']) !!} <span class="invalid-feedback" role="alert">
        <strong></strong>
    </span>
    </div>
    <label for="purchase">{{ __('Order Amount to earn 1') }} {{getNomenclatureName('Loyalty Cards', false)}} {{ __("point") }} ({{ __('as per primary currency') }})</label>
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">1 {{getNomenclatureName('Loyalty Cards', false)}} {{ __('Point') }} </span>
                </div>
                <input type="text" onkeypress="return isNumberKey(event);" class="form-control" value="{{$lc->amount_per_loyalty_point}}" name="amount_per_loyalty_point" id="amount_per_loyalty_point" placeholder="Value" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            </div>
        </div>
    </div>
</div>