@extends('store.layouts.products')
@section('header')
My orders
@endsection
@section('content')
<section class="store-content-section order-type-template store-order-transaction-complete-section">
	<div class="container">
	<div class="row">
			<div class="transaction-complete">
				<div class="transaction-complete-inner">
					<h2>Transaction<br>Complete</h2>
                                        @if(isset($orderNumber))
					<h3 class="title order-id">#{!!$orderNumber!!}</h3>
                                        @endif
					<img src="{{ url('/alchemy/images') }}/transaction-complete-image.svg">
					<p>You can see the details of this order<br>again as part of your history order</p>
					<div class="stickyfooter action-buttons">
						<a href="{!! route('store.orderSearch') !!}">Back To Search</a>
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
                    <p>{{$message}}<br>try it again.</p>
	        	</div>
	        	<div class="modal-footer">
	        		{{ link_to_route('store.orderSearch', 'Try Again','', array())}}
	        	</div>
	        </div>
	    </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    var status = '<?php echo $status; ?>';
    $(document).ready(function () {
        if (!status) {
            $('#error-popup').modal({backdrop: 'static', keyboard: false});
        }
    });
</script>
@endsection