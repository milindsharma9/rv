<li class="menu-address tree-view">
    <a>My Delivery Address</a>
    <div class="tree-child form-group-wrap">
        {!! Form::model($userData, array('files' => true, 'id' => 'form-customer-delivery-address', 'method' => 'POST', 'route' => array('customer.saveAddress'))) !!}
        <?php $userId = $userData['id']; ?>
        @if(isset($userData['userAddress']->fk_users_id))
        <?php $userId = $userData['userAddress']->fk_users_id; ?>
        @endif
        {!! Form::hidden('id', $userId) !!}
        <div class="row">
            <div class="col-xs-12">
                <div id="customer-address" class="alert"></div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('address', 'Address*') !!}
                    <?php $address = $city = $state = $pin = ''; ?>
                    @if(isset($userData['userAddress']->address))
                    <?php $address = $userData['userAddress']->address; ?>
                    @endif
                    {!! Form::text('address', old('address',$address), array('placeholder' => 'address name')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('country', 'Country*') !!}
                     @if(isset($userData['userAddress']->state))
                    <?php $state = $userData['userAddress']->state; ?>
                    @endif
                    {!! Form::text('state', old('state',$state), array('placeholder' => 'country name')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('town_name', 'Town Name*') !!}
                     @if(isset($userData['userAddress']->city))
                    <?php $city = $userData['userAddress']->city; ?>
                    @endif
                    {!! Form::text('city', old('city',$city), array('placeholder' => 'town name')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('postcode', 'Postcode*') !!}
                    @if(isset($userData['userAddress']->pin))
                    <?php $pin = $userData['userAddress']->pin; ?>
                    @endif
                    {!! Form::text('pin', old('pin',$pin), array('placeholder' => 'postcode', 'id' => 'postcode_selected')) !!}
                </div>
            </div>
            <div class="col-xs-12">
                {!! Form::submit('Save Details', array('class' => 'btn btn-submit-profile', 'id' => 'submitEditAddress')) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>
</li>