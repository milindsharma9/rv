<div id="change-address-checkout" class="modal fade zip-code-type-popup" role="dialog">
    <div class="modal-dialog">
        @if(isset($userAddress['address']))
        <div class="modal-content">
            <div class="modal-header modal-header-secondary">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4>Change your delivery address</h4>
            </div>
            <div class="modal-body">
                @if(isset($address) && $address != '')
                <div class="saved-address">
                    <p>Your saved delivery address is</p>
                    <address>{{$address}}</address>
                </div>
                @endif
                @php
                    $selectedPostcode = CommonHelper::getUserCartDeliveryPostcode();
                @endphp
                <p>Please enter a <strong>new</strong> delivery address matching {{$selectedPostcode}}</p>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
                <?php $userAddress = json_decode($userAddress['address']); ?>
                {!! Form::model($userAddress, array('files' => true, 'id' => 'form-customer-delivery-address', 'method' => 'POST', 'route' => array('customer.saveAddress'))) !!}
                @if(isset($userAddress->fk_users_id))
                <?php $userId = $userAddress->fk_users_id; ?>
                @endif
                {!! Form::hidden('id', $userId) !!}
                <div class="form-group">
                    <?php $address = $city = $state = $pin = ''; ?>
<!--                    @if(isset($userAddress->address))
                    <?php // $address = $userAddress->address; ?>
                    @endif-->
                    {!! Form::text('address', old('address',$address), array('placeholder' => 'Address')) !!}
                </div>
                <div class="form-group">
<!--                    @if(isset($userAddress->city))
                    <?php // $city = $userAddress->city; ?>
                    @endif-->
                    {!! Form::text('city', old('city',$city), array('placeholder' => 'Town')) !!}
                </div>
                <div class="form-group">
<!--                    @if(isset($userAddress->state))
                    <?php // $state = $userAddress->state; ?>
                    @endif-->
                    {!! Form::text('state', old('state',$state), array('placeholder' => 'Country')) !!}
                </div>
                <div class="form-group">
                    <input type="text" value="{{CommonHelper::getUserCartDeliveryPostcode()}}" readonly="" name="pin">
                </div>
            </div>
            <div class="modal-footer">
                <div class="action-buttons btn-count-2">
                    {{ link_to_route('customer.cart.clearcart', 'Clear Cart')}}
                    <button id="submit-edit-address-form">Save</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @endif
    </div>
</div>