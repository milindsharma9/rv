@extends('store.layouts.products')
@section('header')
Edit Profile
@endsection
@section('content')
<section class="store-content-section store-wallet-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="title">{{ trans('Enter Transfer Amount') }}</h1>
                @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                            </ul>
                        </div>
                @endif
                @if (\Session::has('message'))
                    {{\Session::get('message')}}
                @endif
            </div>
        </div>
        <div class="wallet-bal">
            Wallet Balance : <span>{{$viewData['amount']}}</span>
        </div>

        {!! Form::open(array('files' => true, 'route' => 'store.payout.initiate', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
            <div class="form-group">
                <div class="col-xs-12 col-sm-6">
                    {!! Form::label('transfer_amount', 'Enter Amount*', ['class' => 'control-label']) !!}
                    {!! Form::text('transfer_amount', '', ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 col-sm-6">
                    @if($viewData['amount'] > 20)
                        {!! Form::submit(trans('admin/events.create'), ['class' => 'btn-red']) !!}
                    @else
                        To initiate payout Minimum Wallet Balance 20 is required.
                    @endif
                </div>
            </div>
        </div>
</section>
        {!! Form::close() !!}
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
                    alert('Error while fetching details|' + result.message);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
    
    function prepareBankDetailData(data) {
        var html = "Account Number : " + data.account_number + "<br />";
        var html = html + "Country : " + data.country + "<br />";
        var html = html + "Owner name : " + data.owner_name + "<br />";
        return html;
    }
</script>
@endsection