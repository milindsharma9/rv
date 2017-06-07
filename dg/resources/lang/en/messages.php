<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the alchemy platform.
    |
    */
    
    /*
     * Common Error Messages
     */
    'exception'                                 => 'Exception|',
    'success'                                   => 'success',
    'validation_error'                          => 'Validation fails',
    /*
     * Order Error Messages
     */
    
    'order_payment_error'                       => 'PaymentError|',
    'order_exception'                           => 'Exception|',
    'order_error'                               => 'Error|',
    'order_error_invalid_order'                 => 'Invalid Order|',
    'order_error_price_mismatch'                => 'Price Mismatch|',
    'common_error'                              => 'Something went Wrong. Please try again.',
    
    /*
     * Payment Error Messages
     */
    'exception_manogopay'                       => 'MangoPayException|',
    'payment_error_3d_secure_not_allowed'       => '3dSecure not allowed',
    
    /*
     * DB Error Messages
     */
    'db_error'                                  => 'DB Error',
    
    /*
     * API Errors
     */
    'api_parameter_missing'                     => 'Required Paramter Missing.',
    'api_cart_unauthorize_error'                => 'Not authorize for Cart services',
    
    /**
     * JWT Errors
     */
    'token_invalid'                             => 'Token is Invalid',
    
    /**
     * customer controller errors
     */
    
    'user_invalid'                              => 'User details Not found',
    'logout_success'                            => 'Logout Successfully',
    'email_success'                             => 'We have sent you email, please verify your account.',
    'email_failure'                             => 'There is some problem while sending email please contact administrator. hello@alchemywings.co',
    'user_update_success'                       => 'User details updated successfully.',
    'user_image_validation'                     => 'Only .jpeg, .jpg fromat allowed.',
    'base64_image_validation'                   => 'Image is not proper base64 encoded: expecting "data:image/{jpeg,jpg};base64".',
    'product_not_found'                         => 'Product Does not Exists.',
    'product_found'                             => 'Product details fetched successfully.',
    'bundle_not_found'                          => 'Bundle Does not Exists.',
    'bundle_found'                              => 'Bundle details fetched successfully.',
    'order_not_found'                           => 'Order Does not Exists.',
    'order_found'                               => 'Order details fetched successfully.',
    
    /*
     * Store Register
     */
    'store_register_success'                   => 'We sent you an activation code. Check your email.',
    'store_register_success_email_subject'     => 'Activate Your Account',
    'store_register_activate_error'            => 'Password is already set for this user, Please try Forgot password.',
    'store_register_activate_exception_error'  => 'Something went wrong, Please get in touch with Admin.',
    'store_address_edit_success'               => 'Address has been Updated.',
    'store_time_success'                       => 'Store details fetched successfully.',

    /*
     *
     */
    'delivery_postcode_set_error'           => 'User Delivery Postcode data not set in session',
    'postcode_error_not_serviceable'        => 'PostCode is not Serviceable.',
    'postcode_error_not_serviceable_new'        => 'This postcode is not available.<br>Please try again or just browse the site.',
    'postcode_error_no_products'            => 'We\'ve not got coverage in your area yet, but we\'re coming soon!',
    'postcode_change_cart_empty_warning'                => 'If you change your postcode. Your cart will be emptied.',
    'postcode_change_cart_empty_warning_postcode'       => 'Current delivery Postcode is : ',
    'postcode_not_found'                                => 'PostCode not found. ',
    'card_not_found'                                => 'Card not found. ',
    
    'store_timings_updated'                 => 'Timings updated successfully',
    'password_change_success'               => 'Password Updated Successfully',

    /**
     * File validation Rules
     */
    'invalid_file_format'           => 'Invalid File Format. Allowed File Formats are : ',
    'max_upload_size_increase'      => 'Max upload file size is 2 MB.',
    'incorrect_dimensions'          => 'Incorrect File dimensions',
    
    /**
     * Messages for checkout page api.
     * 
     */
    
    'address_not_found'           => 'Please add delivery address.',
    'postcode_not_same'      => 'Post Code is not same as set before.',
    'incorrect_dimensions'          => 'Incorrect File dimensions',
    'card_details_not_found'           => 'Please add card details.',
    'details_found'                => 'Details fetched successfully.',
    'postcode_reset_cart_error'      => 'Changing your delivery postcode will empty your cart, do you want to continue?',
    
    'invalid_phone_number'          => 'Not a valid UK Phone number.',
    'validate_phone_api_error'      => 'API Response Error.',
    
    'email_order_exception'                           => 'Order Email Exception|',
    'empty_cart_message'                           => 'No Item in cart',
    'company_house_api_error'      => 'API Error: Company House',
    
    /**
     * Messages for coupon.
     * 
     */
    'coupon_already_applied'                   => 'Coupon already applied on order.',
    'coupon_expired'                           => 'Looks like the code you are trying to use as already expired. Please try another code or proceed without a code.',
    'coupon_invalid'                           => 'The coupon code you are trying to use doen\'t exist. Please enter a valid code.',
    'coupon_apply_success'                     => 'Coupon applied successfully.',
    'coupon_apply_error'                       => 'Error while applying Coupon.',
    'coupon_valid'                             => 'Valid Coupon Code.',
    'coupon_already_used_by_user'              => 'You have already used this coupon earlier.',
    'coupon_usage_exceeds'                     => 'The code you are trying to use was already used. Please enter another code.',
    'coupon_exception'                         => 'Coupon Exception|',
    'vendor_kyc_not_found'                     => 'You are not live yet, complete your registration ',
    
    /**
     * Order Status Messages
     */
    'order_status_change_invalid'              => 'Not valid status',
    'order_status_change_no_data'              => 'No data found for order',
    'order_status_change_success'              => 'Order status changed successfully',
    'order_status_change_refund_message'       => '(Refunded to your payment card.)',
    'admin_payment_failed'                     => 'Payment to admin wallet failed please try again.',
    'admin_unsufficient_balance'               => 'Sufficient balance in admin wallet is not available, Please Update the balance and try again.',
    
    /**
     * 
     */
    'fullfillment_api_less_products'           => 'Product Count Mismatch | Api returns less number of products.',
    'fullfillment_api_failure'                 => 'Checkout Exception|FullFillMent APi No Response.',
    'checkout_exception'                       => 'Checkout Exception|',
    'checkout_error_product_missing'           => 'Product data missing from stores.',
    
    'vendor_apply_success'                   => 'Details saved. We will get in touch with you.',
    
];
