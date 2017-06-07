<li class="menu-contact tree-view">
    <a>My Contact Details</a>
    <div class="tree-child form-group-wrap">
        {!! Form::model($user, array('files' => true, 'id' => 'form-customer-profile')) !!}
        {!! Form::hidden('id', $user->id) !!}
        <div class="row">
            <div class="col-xs-12">
                <div id="customer-profile" class="alert"></div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('first_nameL', 'First Name*') !!}
                    {!! Form::text('first_name', old('name',$user->first_name), array('placeholder'=>'First Name')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('last_name', 'Last Name*') !!}
                    {!! Form::text('last_name', old('name',$user->last_name), array('placeholder'=>'Last Name')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('email', 'Email') !!}
                    {!! Form::text('email', old('email',$user->email), array('readonly' => 'readonly')) !!}
                </div>
                <div class="form-group phone-group">
                    {!! Form::label('phone', 'Phone') !!}
                    <span class="phone-prefix">+44 0</span>
                    {!! Form::text('phone', old('phone',$user->phone), array('placeholder'=>'Phone Number', 'id' => 'phone_display_input')) !!}
                </div>
            </div>
            <div class="col-xs-12">
                {!! Form::submit('Save Details', array('class' => 'btn btn-submit-profile', 'id' => 'submitEditProfile')) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</li>