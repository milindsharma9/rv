<!-- Add Store Modal -->
<div id="add-store" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4>Add Store</h4>
            </div>
            <div class="modal-body">
                <form id="vendor_add_new_store">
                    <div class="form-group">
                        <label class="control-label">Store name</label>
                        <input type="text" value="" name="name" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="text" value="" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Store street address</label>
                        <input type="text" value="" name="address" id="address" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Store city</label>
                        <input type="text" value="" name="city" id="city" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Store Postcode</label>
                        <input type="text" value="" name="pin" id="pin" class="form-control">
                    </div>
                    <span class="help-block" style="color: rgb(169, 68, 66);"></span>
                    <button>Submit</button>
                </form>
            </div>
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        </div>
    </div>
</div>
@section('javascript_1')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script type="text/javascript">
    var addNewStoreUrl     = "{!! route('store.add.store')!!}";
    $("#vendor_add_new_store").validate({
        focusInvalid: true,
        debug: true,
        rules: {
            name: 'required',
            email: 'required',
            address: 'required',
            city: 'required',
            pin: 'required',
            },
        submitHandler: function (form) {
            var storeName = $('#name').val();
            var address = $('#address').val();
            var city = $('#city').val();
            var pin = $('#pin').val();
            var email = $('#email').val();
            var storeDetails = {store_name: storeName, address: address, city: city, pin: pin, email: email};
            $.ajax({
                url: addNewStoreUrl,
                method: 'POST',
                data: {
                    data:storeDetails,
                    _token: $('input[name=_token]').val()
                },
                success: function(result) {
                    if(result.status) {
                        window.location = window.location.href;
                    } else {
                        $('.help-block').html(result.message);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("Some error. Please try refreshing page.");
                }
            });
        }
    });
</script>
@endsection
