<li class="menu-change-pass tree-view">
    <a>Change Password</a>
    <div class="tree-child form-group-wrap">
        {!! Form::open(array('files' => false,'id' => 'form-customer-change-password')) !!}
        {!! Form::hidden('requestAjax', 1) !!}
        <div class="row">
            <div class="col-xs-12">
                <div id="customer-password" class="alert"></div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('password', 'New Password*') !!}
                    <input name="password" type="password" value="{{old('password')}}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Confirm Password*') !!}
                    <input name="password_confirmation" type="password" value="{{old('password_confirmation')}}">
                </div>
            </div>
            <div class="col-xs-12">
                {!! Form::submit('Save Details', array('class' => 'btn btn-submit-profile')) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</li>