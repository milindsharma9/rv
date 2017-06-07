{!! Form::open(array('route' => $driverFormRoute, 'id' => 'driver-apply-form')) !!}
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>City*</label>
                    {!! Form::text('city', old('city', 'London'), ['class' => 'form-control', 'placeholder' => 'address name']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Vehicle*</label>
                    {!! Form::select('vehicle', config('rider_configurations.vehicle_type'), null, array('class' => '')) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>First Name*</label>
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'First Name']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Surname*</label>
                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => 'Surname']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Email*</label>
                    {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Mobile Phone*</label>
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '1234567890']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Address Line 1*</label>
                    {!! Form::text('address_line_1', old('address_line_1'), ['class' => 'form-control', 'placeholder' => 'Address Line 1']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Address Line 2</label>
                    {!! Form::text('address_line_2', old('address_line_2'), ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Town*</label>
                    {!! Form::text('region', old('region'), ['class' => 'form-control', 'placeholder' => 'town name']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>County*</label>
                    {!! Form::text('country', old('country'), ['class' => 'form-control', 'placeholder' => 'county name']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Postcode*</label>
                    {!! Form::text('pin', old('pin'), ['class' => 'form-control', 'placeholder' => 'postcode']) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Nationality*</label>
                    {!! Form::select('nationality', config('appConstants.country_select'), null, array('class' => '')) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Current occupation*</label>
                    {!! Form::select('fk_occupation_id', config('rider_configurations.driver_occupation'), null, array('class' => '')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>right to work*</label>
                    <label class="check-option">
                        {!! Form::checkbox('is_right_to_work', '1',  old('is_right_to_work'), []) !!}
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
                                {!! Form::checkbox('delivery_area[]', 'east_london',  old('delivery_area[]'), []) !!}East London
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'central_london',  old('delivery_area[]'), []) !!}Central London
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'south_london',  old('delivery_area[]'), []) !!}South London
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="check-option">
                                {!! Form::checkbox('delivery_area[]', 'west_london',  old('delivery_area[]'), []) !!}West London
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
                <div class="col-sm-6 col-xs-12 vehical-type-scooter" style="display: none">
                    @php
                        $scooterQuestions = config('rider_configurations.scooter_question');
                    @endphp
                    @foreach ($scooterQuestions as $questionId => $question)
                        <div class="form-group">
                            <label>{{$question}}</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    {{ Form::radio("question_".$questionId, "1" , false) }} Yes
                                </label>
                                <label class="radio-option">
                                    {{ Form::radio("question_".$questionId, "0" , false) }} No
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-sm-6 col-xs-12 vehical-type-bicycle">
                    @php
                        $bicycleQuestions = config('rider_configurations.bicycle_question');
                    @endphp
                    @foreach ($bicycleQuestions as $questionId => $question)
                        <div class="form-group">
                            <label>{{$question}}</label>
                            <div class="radio-group">
                                @if ($questionId == 2)
                                    {!! Form::select('question_'.$questionId, config('rider_configurations.miles_per_week_select'), null, array('class' => '')) !!}
                                @else
                                    <label class="radio-option">
                                        {{ Form::radio("question_".$questionId, "1" , false) }} Yes
                                    </label>
                                    <label class="radio-option">
                                        {{ Form::radio("question_".$questionId, "0" , false) }} No
                                    </label>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-sm-6 col-xs-12">
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
                                                    {!! Form::checkbox('availability[]', $dayName . '_' . $timeId , '', []) !!}
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
            <input type="submit" name="" value="Save Details">
        </div>
{!! Form::close() !!}