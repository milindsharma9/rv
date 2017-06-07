@extends('admin.layouts.master')
@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('Enter Transfer Amount') }}</h1>
        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
    <div class="form-group row">
        <label class="col-sm-2 control-label" style="text-align:right;">Wallet Balance :</label>
        <div class="col-sm-10">{{$viewData['amount']}}</div>
    </div>
        
        {!! Form::open(array('files' => true, 'route' => 'admin.payout.initiate', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
            <div class="form-group">
                {!! Form::label('transfer_amount', 'Enter Amount*', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::text('transfer_amount', '', ['class' => 'form-control']) !!}
                    {!! Form::hidden('user_id', $userId, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    @if($viewData['amount'] > 20)
                        {!! Form::submit(trans('admin/events.create'), ['class' => 'btn btn-primary']) !!}
                    @else
                        To initiate payout Minimum Wallet Balance 20 is required.
                    @endif
                </div>
            </div>
        {!! Form::close() !!}
@endsection
@section('javascript')
<script type="text/javascript">
    var getBankDetailsUrl = "{!! route('admin.bank.get'); !!}";
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