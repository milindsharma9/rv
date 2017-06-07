<div id="zip-code-popup" class="modal fade zip-code-type-popup" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Delivery Address</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            @php
                $isCartEmpty = CommonHelper::isCartEmpty();
            @endphp
            @if ($isGuest)
                <!-- No Logged In -->
                <div class="not-logged-in">
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label>Enter Postcode</label>
                                <input type="text" placeholder="E.G. N4 2PG" id="postcode_auto" name="pin">
                            </div>
                        </form>
                        <span class="or-seperator">OR</span>
                        <div class="login-link">
                            <a data-toggle="modal" data-target="#login-register">Login</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{--{{ link_to_route('store.orderSearch', 
                            'Search', '', 
                            array('class' => 'cart_popup_postcode_set', 'type' => 'button'))}}--}}
                        <button class="cart_popup_postcode_set">Search</button>
                    </div>
                </div>
            @else
                @if(empty($deliveryPostcode))
                    <!-- Logged In with No Zipcode -->
                    <div class="logged-in-no-zip first-time-zip">
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label>Enter Postcode</label>
                                    <input type="text" placeholder="E.G. N4 2PG" id="postcode_auto" name="pin">
                                    @if(!$isCartEmpty)
                                        <div class="suggestion-text">{{trans('messages.postcode_reset_cart_error')}}</div>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="cart_popup_postcode_set">Search</button>
                        </div>
                    </div>
                @else
                    <!-- Logged In with Zipcode -->
                    <div class="logged-in-no-zip" style="display:none;">
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label>Enter Postcode</label>
                                    <input type="text" placeholder="E.G. N4 2PG" id="postcode_auto" name="pin">
                                    @if(!$isCartEmpty)
                                        <div class="suggestion-text">{{trans('messages.postcode_reset_cart_error')}}</div>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                                <button class="cart_popup_postcode_set">Search</button>
                        </div>
                    </div>
                    
                    <div class="logged-in-zip">
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label>Do you want to continue with below postal code?</label>
                                    <input type="text" placeholder="E.G. N4 2PG" name="pin" value="{{$deliveryPostcode}}" readonly="">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <div class="action-buttons btn-count-2">
                                <button class="cart_popup_postcode_set">Accept</button>
                                <button class="edit-zip-code">Edit</button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            <div class="unservicable-zipcode" style="display:none;">
                <div class="modal-body">
                    <img src="{{ url('alchemy/images') }}/zip-error.svg">
                    <h3>Oh no!</h3>
                    <p id="postcode_msg_p">This postcode is not available.<br>Please try again or just browse the site.</p>
                </div>
                <div class="modal-footer">
                    <div class="action-buttons">
                        <button type="button" class="try_zipcode">Change Postcode</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>