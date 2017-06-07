@extends('admin.layouts.master')

@section('content')

<div class="sign-up">
    <div class="col-xs-12">
        <h1>{{ trans('admin/drivers.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
    <div class="col-xs-12">
        <div class="col-xs-12">
{!! Form::model($driver, array('files' => true, 'class' => '', 'id' => 'driver-apply-form', 'method' => 'PATCH', 'route' => array('admin.drivers.update', $driver->id))) !!}
<div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>City*</label>
                    {!! Form::text('city', old('city', $driver->city), ['class' => 'form-control', 'placeholder' => 'address name']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Vehicle*</label>
                    {!! Form::select('vehicle', config('rider_configurations.vehicle_type'), $driver->vehicle, array('class' => '')) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>First Name*</label>
                    {!! Form::text('name', old('name', $driver->first_name), ['class' => 'form-control', 'placeholder' => 'First Name']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Surname*</label>
                    {!! Form::text('last_name', old('last_name', $driver->last_name), ['class' => 'form-control', 'placeholder' => 'Surname']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Email*</label>
                    {!! Form::text('email', old('email', $driver->email), ['class' => 'form-control', 'placeholder' => 'Email', 'readonly' => true]) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Mobile Phone*</label>
                    {!! Form::text('phone', old('phone', $driver->phone), ['class' => 'form-control', 'placeholder' => '1234567890']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Address Line 1*</label>
                    {!! Form::text('address_line_1', old('address_line_1', $driver->address_line_1), ['class' => 'form-control', 'placeholder' => 'Address Line 1']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Address Line 2</label>
                    {!! Form::text('address_line_2', old('address_line_2', $driver->address_line_2), ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Town*</label>
                    {!! Form::text('region', old('region', $driver->region), ['class' => 'form-control', 'placeholder' => 'town name']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>County*</label>
                    {!! Form::text('country', old('country', $driver->country), ['class' => 'form-control', 'placeholder' => 'county name']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Postcode*</label>
                    {!! Form::text('pin', old('pin', $driver->pin), ['class' => 'form-control', 'placeholder' => 'postcode']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Nationality*</label>
                    {!! Form::select('nationality', config('appConstants.country_select'), $driver->nationality, array('class' => '')) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Current occupation*</label>
                    {!! Form::select('fk_occupation_id', config('rider_configurations.driver_occupation'), $driver->fk_occupation_id, array('class' => '')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>right to work*</label>
                    <label class="check-option">
                        {!! Form::checkbox('is_right_to_work', '1',  $driver->is_right_to_work, []) !!}
                        I have the right to work in the UK
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Preferred delivery area/s*</label>
                    <div class="row">
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'east_london',  $driver->east_london, []) !!}East London
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'central_london',  $driver->central_london, []) !!}Central London
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'south_london',   $driver->south_london, []) !!}South London
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'west_london',  $driver->west_london, []) !!}West London
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <h3 class="section-title"><span>For <span id='selected_vehicle_type_span'>bicycle</span> drivers</span></h3>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 vehical-type-scooter" style="display: none">
                    @php
                        $scooterQuestions = config('rider_configurations.scooter_question');
                    @endphp
                    @foreach ($scooterQuestions as $questionId => $question)
                        @php
                            $yesChecked = $noChecked = false;
                        @endphp
                        @if ($driver->vehicle == config('rider_configurations.vehicle_type_scooter'))
                            <?php
                                $selQRadioval = isset($driver->responses[$questionId]) ? $driver->responses[$questionId] : 2;
                                if ($selQRadioval == 1) {
                                    $yesChecked = true;
                                }
                                if ($selQRadioval == 0) {
                                    $noChecked = true;
                                }
                            ?>
                        @endif
                        <div class="form-group">
                            <label>{{$question}}</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    {{ Form::radio("question_".$questionId, "1" , $yesChecked) }} Yes
                                </label>
                                <label class="radio-option">
                                    {{ Form::radio("question_".$questionId, "0" , $noChecked) }} No
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12 col-sm-6 vehical-type-bicycle">
                    @php
                        $bicycleQuestions = config('rider_configurations.bicycle_question');
                        $selQval = '';
                    @endphp
                    @foreach ($bicycleQuestions as $questionId => $question)
                        @php
                            $yesChecked = $noChecked = false;
                        @endphp
                        @if ($driver->vehicle == config('rider_configurations.vehicle_type_bicycle'))
                            <?php
                                $selQRadioval = $driver->responses[$questionId];
                                if ($selQRadioval == 1) {
                                    $yesChecked = true;
                                }
                                if ($selQRadioval == 0) {
                                    $noChecked = true;
                                }
                            ?>
                        @endif
                        <div class="form-group">
                            <label>{{$question}}</label>
                            <div class="radio-group">
                                @if ($questionId == 2)
                                    @if ($driver->vehicle == config('rider_configurations.vehicle_type_bicycle'))
                                        @php
                                            $selQval = $driver->responses[$questionId];
                                        @endphp
                                    @endif
                                    {!! Form::select('question_'.$questionId, config('rider_configurations.miles_per_week_select'), $selQval, array('class' => '')) !!}
                                @else
                                    <label class="radio-option">
                                        {{ Form::radio("question_".$questionId, "1" , $yesChecked) }} Yes
                                    </label>
                                    <label class="radio-option">
                                        {{ Form::radio("question_".$questionId, "0" , $noChecked) }} No
                                    </label>
                                @endif
                                
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label>What is your current availability?*</label>
                        <table class="table-availability">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Day</th>
                                    <th>Evening</th>
                                    <th>Late Night</th>
                                    <th>All</th>
                                </tr>
                            </thead>
                            @php
                                $availableDay = config('rider_configurations.driver_available_day');
                                $availableTime = config('rider_configurations.driver_available_time');
                            @endphp
                            <tbody>
                                @foreach($availableDay as $dayId =>  $dayName)
                                    <tr>
                                        <td>{{$dayName}}</td>
                                        @foreach($availableTime as $timeId => $time)
                                            <td>
                                                <label class="check-option">
                                                    <?php
                                                        $selDayVal = false;
                                                        if (isset($driver->availability_select[$dayName . '_' . $timeId])) {
                                                            $selDayVal = true;
                                                        }
                                                    ?>
                                                    {!! Form::checkbox('availability[]', $dayName . '_' . $timeId , $selDayVal, []) !!}
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="check-option">
                    {!! Form::checkbox('activated', '1', $driver->activated, ['class' => '']) !!} Active
                </label>   
            </div>
            <input type="submit" name="" value="Save Details">
        </div>
        </div>
        </div>
{!! Form::close() !!}

@endsection

@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('alchemy/js') }}/driver_apply.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@endsection