<div class="panel panel-default change-password-panel">
    <div class="panel-body">
        {!! Form::open(array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-user-change-password', 'method' => 'POST', 'route' => array($routePrefix.'.changePassword.post'))) !!}
        <div class="order-header hidden-xs">
            <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">&lt; Back</a>Change Password</h3>
        </div>
        <div class="row order-header visible-xs">
            <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">&lt; Back</a>Change Password</h3>
        </div>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
            </ul>
        </div>
        @endif
        @if ( session()->has('message') )
            <div class="alert alert-success alert-dismissable">{{ session()->get('message') }}</div>
        @endif
        <div class="form-group">
            {!! Form::label('password', 'New Password*', array('class'=>'col-xs-12 control-label')) !!}
            <div class="col-xs-12">
                <input name="password" type="password" value="{{old('password')}}">
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('password_confirmation', 'Confirm Password*', array('class'=>'col-xs-12 control-label')) !!}
            <div class="col-xs-12">
                <input name="password_confirmation" type="password" value="{{old('password_confirmation')}}">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                {!! Form::submit('Update', array('class' => 'btn btn-submit-profile')) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
</div>