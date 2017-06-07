@section('title')
Alchemy - Order Success
@endsection
@extends('customer.layouts.customer')
@section('content')

<section class="customer-content-section order-type-template customer-order-transaction-complete-section">
	<div class="container">
	<div class="row">
			<h2 class="hidden-xs">Thank you for your order</h2>
			<div class="transaction-complete">
				<div class="transaction-complete-inner">
					<h2 class="visible-xs">Thank you<br>for your order</h2>
                    @if(isset($orderNumber))
						<h3 class="title order-id">
							<span>Your order number is</span>
							{!!$orderNumber!!}
						</h3>
                    @endif
					<img src="{{ url('/alchemy/images') }}/transaction-complete-image.svg">
					<p>Your order will be on it's way soon<br> Please have your ID ready</p>
					<div class="stickyfooter action-buttons">
						<a href="{!! route('customer.order.track', $orderNumber) !!}">Track Order</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="error-popup" class="modal fade" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal"></button>-->
                </div>
	        	<div class="modal-body">
	        		<img src="{{ url('/alchemy/images') }}/broken-cycle.svg">
	        		<h2>SomeThing went wrong</h2>
                                <p>{{$message}}<br>Try it again.</p>
	        	</div>
	        	<div class="modal-footer">
	        		{{ link_to_route('customer.checkout', 'Try Again','', array())}}
	        	</div>
	        </div>
	    </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    var status = '<?php echo $status; ?>';
    var orderId = "{{ (isset($orderNumber) ? $orderNumber : 0)}}";
    var message = "{{ (isset($message ) ? $message : '')}}";
    $(document).ready(function () {
        if (!status) {
            $('#error-popup').modal({backdrop: 'static', keyboard: false});
            //track intercom.io make purchase event.
            var metaData = {reason: message};
            trackIntercomEvent('make-payment-failure', metaData);
        }else{
            //track intercom.io make purchase event.
            var metaData = {orderId: orderId};
            trackIntercomEvent('make-payment-success', metaData);
        }
    });
</script>
<script>
    //to be used in Google Tag Manager
  dataLayer = [{
    'id': "{!!$orderNumber!!}",
    'amount': '{!!$cartTotal!!}'
  }];
</script>
@endsection