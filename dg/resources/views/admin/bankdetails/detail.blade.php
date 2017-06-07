@extends('admin.layouts.master')
@section('content')
    <h3>Already has bank account :</h3>
    <div class="row">
        <div class="col-sm-3 col-sm-12">
            <div id="bank_details">
                <button class="btn btn-success bank_details_show">Show Details</button>
            </div>
        </div>
        <div class="col-sm-3 col-sm-12">
            <p>{!! link_to_route('admin.bank.form', trans('Change Details') , "", array('class' => 'btn btn-success')) !!}</p>
        </div>
        <div class="col-sm-3 col-sm-12">
            <p>{!! link_to_route('admin.payout', trans('PayOut') , "", array('class' => 'btn btn-success')) !!}</p>
        </div>        
        <div class="col-sm-3 col-sm-12">
            <p>{!! link_to_route('admin.payout.detail', trans('View PayOut Details') , "1", array('class' => 'btn btn-success')) !!}</p>
        </div>
    </div>
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
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
        var html = html + "Type : " + data.type + "<br />";
        var html = html + "Owner name : " + data.owner_name + "<br />";
        return html;
    }
</script>
@endsection