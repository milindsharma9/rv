@extends('store.layouts.products')
@section('header')
Edit Profile
@endsection
@section('content')
<section class="store-content-section section-store-bank-details">
    <div class="container">
        <div class="row">
                <div class="order-header">
                    <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a> <span>Already has bank account :</span></h3>
                </div>
                <div class="col-xs-12 col-sm-4 center">
                    <button class="btn-red bank_details_show">Show Details</button>
                    <div id="bank_details"></div>
                </div>
                <div class="col-xs-12 col-sm-4 center">
                    {!! link_to_route('store.bank.form', trans('Change Details') , "", array('class' => 'btn-red')) !!}
                </div>
                {{--<div class="col-xs-12 col-sm-4 center">
                    {!! link_to_route('store.payout', trans('PayOut') , "", array('class' => 'btn-red')) !!}
                </div>--}}
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        </div>
    </div>
</section>
@endsection
@section('javascript')
<script type="text/javascript">
    var getBankDetailsUrl = "{!! route('store.bank.get'); !!}";
    $(".bank_details_show").click(function() {
        $('#bank_details').html("Fetching Details...");
        $.ajax({
            url: getBankDetailsUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                //data:JSON.stringify(productArr),
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if(result.status) {
                    var content = prepareBankDetailData(result.data);
                    $('#bank_details').html(content);
                } else {
                    alert('Error while fetching details|' + result.message + ' \nPlease try refreshing page.');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
    
    function prepareBankDetailData(data) {
        var html = "Account Number : " + data.account_number + "<br />";
        var html = html + "Type : " + data.type + "<br />";
        var html = html + "Owner name : " + data.owner_name + "<br />";
        return html;
    }
</script>
@endsection