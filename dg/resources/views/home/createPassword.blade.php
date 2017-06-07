@extends('layouts.default')
@section('header')
<a href="{{ route('store.dashboard') }}">Alchemy</a>
@endsection
@section('content')
<section class="register-content-section">
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">Create Password</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/home/createPassword') }}">
                        {!! csrf_field() !!}
                        <input type="hidden" class="form-control" name="activated" value="1">
                        <input type="hidden" class="form-control" name="id" value="{{$userId}}">
                        <div class="form-group">
                            <label class="col-md-12 control-label">Password</label>

                            <div class="col-md-12 {{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12 control-label">Confirm Password</label>

                            <div class="col-md-12 {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection