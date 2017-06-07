@extends('admin.layouts.master')

@section('header')
Opening Times
@endsection
@section('content')
<section class="store-content-section availability-section">
    <div class="container">
        {!! Form::open(array('route' => 'admin.configurations.tookan.update', 'id' => 'store-time', 'class' => 'form-horizontal tookanform', 'method' => 'POST')) !!}
        <div class="row">
            <div class="order-header">
                <h3 class="title"><a href="{{ route('admin.configurations.manage') }}" class="btn-red">< Back</a> <span>Set Tookan Availability</span> 
                    <span class="time-heading visible-xs"><span>24h</span><span>Closed</span></span>
                </h3>
            </div>              
            <div>
            <label> 
                @php
                    $days = config('appConstants.store_days');
                @endphp
            <select name="theDay" id='theDay' class="form-control">                   
                @foreach($days  as $day )
                <span><option value="{{ $day }}" > {{ $day }}</option>         </span>           
                @endforeach                
            </select>
                </label>
            </div>
            <div id="getdata">
                
            </div>
        </div>
        <div class="stickyfooter action-buttons btn-count-2">
            {{ link_to_route('admin.configurations.tookan.show', 'Reset')}}
            <button id="saveTime" class="btn-order-accept">Save</button>
            <!--{{ link_to_route('store.saveTime', 'Save', '', array('class' => 'btn-order-accept'))}}-->
        </div>
        {!! Form::close() !!}

    </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
    var getTookanAvailabilityUrl = "{!! route('admin.getTookanAvailability')!!}";
    $(document).ready(function() {
        opening_time();
        $("#theDay").change(function() {
            opening_time();
        });
    });

    function opening_time() {
        $.ajax({
            url: getTookanAvailabilityUrl,
            method: 'POST',
            data: {
                theDay: $("#theDay").val()
            },
            success: function(result) {
                if (result.status) {
                    $('#getdata').html(result.html_content);
                } else {
                    alert("Something went wrong. Please try refreshing page.");
                }
            },
        });
    }
</script>
@endsection
