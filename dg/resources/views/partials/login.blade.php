<!-- Login/Register Modal -->
<div id="login-register" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <ul class="nav nav-tabs tab-level-1">
                <li class="active"><a data-toggle="tab" href="#login-pane" class="shopper-btn"><span>Shopper</span></a></li>
                <li class=""><a data-toggle="tab" href="#login-pane" class="store-btn"><span>Store</span></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="login-pane">
                    <div class="login-section">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"></button>
                            <p>Need an account?</p>
                            <a class="action-register">Register</a>
                        </div>
                        {{--@if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @endif--}}
                        @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                        @endif
                    <div class="modal-body">
                        <h3><img src="{{ url('alchemy/images') }}/logo-icon.svg"/>Login</h3>
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}" id="login-form">
                            {!! csrf_field() !!}
                            <label>Email</label>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <input type="email" class="form-control" name="email" id='login-email' value="">
                                <span  class="help-block"></span>
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <label>Password</label>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" class="form-control" name="password" id='login-password'>
                                <span  class="help-block"></span>
                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <a href="{!! url('/resetPassword') !!}" class="forgot-pass">Forgotten password?</a>
                            <input type="submit" value="Login" id="login">
                        </form>
                    </div>
            </div>
            <div class="register-section">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"></button>
                    <p>Already have an account?</p>
                    <a class="action-login">Login</a>
                </div>
                <div class="modal-body">
                    <h3><img src="{{ url('alchemy/images') }}/logo-icon.svg"/>Register</h3>
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}"  id="register-form">

                        <div class="row">
                            <div class="col-xs-12">
                                <div id="form-error"></div>
                                {!! csrf_field() !!}
                                <input type="hidden" class="form-control" name="activated" value="1">
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group{{ $errors->has('fname') ? ' has-error' : '' }}">
                                    <label>First name</label>
                                    <input type="text" class="form-control" name="fname" id='register-fname' value="{{ old('fname') }}">
                                    <span  class="help-block"></span>
                                    @if ($errors->has('fname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fname') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group{{ $errors->has('lname') ? ' has-error' : '' }}">
                                    <label>Last name</label>
                                    <input type="text" class="form-control" name="lname" id='register-lname' value="{{ old('lname') }}">
                                    <span  class="help-block"></span>
                                    @if ($errors->has('lname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lname') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" id='register-email' value="{{ old('email') }}">
                                    <span  class="help-block"></span>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group phone-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label>Phone</label>
                                    <span class="phone-prefix">+44 0</span><input type="text" class="form-control" name="phone" id='register-phone' value="{{ old('phone') }}">
                                    <span  class="help-block"></span>
                                    @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label>Password</label>
                                    <input type="password" class="form-control" name="password" id='register-password'>
                                    <i> </i>
                                    <span  class="help-block"></span>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label>Repeat Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" id='register-password_confirmation'>
                                    <span  class="help-block"></span>
                                    @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php
                            $cmsFooterData = \App\Http\Helper\CommonHelper::getFooterCmsLinks();
                        @endphp
                        <?php
                        $privacyRoute = $termsRoute = '';
                        foreach($cmsFooterData as $legalPage) {
                            if ($legalPage['title'] == config('cms.page_privacy')) {
                                $privacyRoute = route($legalPage['user_type'].'.page', $legalPage['url_path']);
                            }
                            if ($legalPage['title'] == config('cms.page_legal')) {
                                $termsRoute = route($legalPage['user_type'].'.page', $legalPage['url_path']);
                            }
                        }
                        ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                <label class="check-option">
                                    <input type="checkbox" name="term-condition" id="term-condition">
                                    I have read the <a target="_blank" href="{{$termsRoute}}">Terms and Conditions </a>
                                    and <a target="_blank" href="{{$privacyRoute}}">Privacy Policy</a>, and agree to bound by them. 
                                    <span class="help-block" style="color: rgb(169, 68, 66);"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="check-option checked">
                                    <input type="checkbox" name="is_subscribe" id="is_subscribe" checked="checked" value="1">
                                    Join our mailing list
                                    <span class="help-block" style="color: rgb(169, 68, 66);"></span>
                                </label>
                            </div>
                            <input type="submit" value="Register">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
