@extends('store.layouts.products')
@section('header')
My orders
@endsection
@section('content')
<section class="store-content-section order-type-template sales-rider-confirmation-section">
    <form role="form" method="POST" action="{{ route('store.verifyVendor') }}">
	<div class="container">
		<div class="row">
			<div class="order-header">
				<h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a>
                                    Order @if(isset($orderNumber)) #{!!$orderNumber!!} @endif</h3>
			</div>
			<div class="rider-conf-wrap">
				<div class="rider-conf-inner">
					<img src="{{ url('/alchemy/images') }}/rider.svg">
					<p>Please, give the device to the driver to verify the information</p>
	                @if ($errors->any())
	                    <div class="alert alert-danger">
	                        <ul>
	                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
	                    </ul>
	                    </div>
	                @endif
                    {!! csrf_field() !!}
                    <div class="form-group">
						<label>Drive iD</label>
						<input type="text" required="required" name="riderEmail" value="{{ old('riderEmail') }}" placeholder="E.G. 123456789" >
                    </div>
                    <div class="form-group">
						<label>Pin</label>
						<input type="password" placeholder="password" name='riderPassword' required="required"/>
                    </div>
					<div class="stickyfooter action-buttons">
						<button id='submit-confirm'>Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
        </form>
	<div id="error-popup" class="modal fade" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
	        	<div class="modal-body">
	        		<img src="{{ url('/alchemy/images') }}/broken-cycle.svg">
	        		<h2>Order number not found</h2>
	        		<p>This order number doesn’t exist.<br>
	        		Please, verify the details and <br>try it again.</p>
	        	</div>
	        	<div class="modal-footer">
	        		<button type="button" data-dismiss="modal">Try Again</button>
	        	</div>
	        </div>
	    </div>
    </div>
</section>
@endsection
@section('javascript')
<script>
    $('#submit-confirm').click(function(e){
        if($('[name=riderEmail]').val() == '' || $('[name=riderPassword]').val() == ''){
            alert('Please provide rider credentials.')
            e.preventDefault();
        }else{
            $('#submit-confirm').trigger('submit');
        }
    });
</script>
@stop