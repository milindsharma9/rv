@extends('store.layouts.products')
@section('header')
Opening Times
@endsection
@section('content')
<section class="store-content-section availability-section">
    <div class="container">
        {!! Form::open(array('route' => 'store.saveTime', 'id' => 'store-time', 'class' => 'form-horizontal', 'method' => 'POST')) !!}
        <div class="row">
            <div class="order-header">
                <h3 class="title"><a href="{{ route('store.profile') }}" class="btn-red">< Back</a> <span>Opening Times</span>
                </h3>
            </div>              
            <div class="days-selector">
            <select name="theDay" id='theDay'>                   
                @foreach($days  as $day )
                <option value="{{ $day }}" > {{ $day }}</option>      
                @endforeach                
            </select>
            </div>
            <div id="getdata">

            </div>
            {!! Form::hidden('storeId', $storeId) !!}

        </div>
        <div class="stickyfooter action-buttons btn-count-2">
            {{ link_to_route('admin.site.time', 'Reset')}}
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
    var getsiteinfo = "{!! route('store.getSiteOpeningInfo')!!}";
    $(document).ready(function() {
        opening_time();
        $("#theDay").change(function() {
            opening_time();
        });
        $('#store-time').on('submit', function() {
            var store_id = [];
            if ($("input[name=schedule]:checked").val() == "set_time") {
                $('[id^=time-]').each(function() {
                    if ($(this).is(':checked') == true) {
                        store_id.push($(this).attr('id'));

                    }
                });
                console.log(store_id.length);
                if (store_id.length < 1) {
                    $("#tt").prepend('<div id="msg" class="alert alert-danger">Please select time slots.</div>');
                    return false;
                }
            }
        });
    });

    function opening_time() {
        $.ajax({
            url: getsiteinfo,
            method: 'POST',
            //dataType: 'json',
            data: {
                StoreId : $("input[name=storeId]").val(),
                theDay: $("#theDay").val()
            },
            success: function(result) {
                $('#getdata').html(result);
                myjs();
                $("input[name=schedule]:radio").change(function() {
                    myjs();
                });
            },
        });
    }

    function myjs() {
        var radioValue = $("input[name=schedule]:checked").val();
        if (radioValue == "is_24hrs" || radioValue == "is_closed") {
            $('[id^=time-]').each(function() {
                $(this).attr("disabled", true);
            });
        } else {
            $('[id^=time-]').each(function() {
                $(this).removeAttr('disabled');
            });
        }
    }
</script>
@endsection
