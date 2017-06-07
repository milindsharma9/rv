<?php

/**
 * These configuration options are not required.
 *
 * The properties below are set directly on MangoPay\MangoPayApi()->Config
 * during instantiation of the MangopayAPI class.
 *
 * @see \MangoPay\Libraries\Configuration
 */

return [

    /**
     * Absolute path to file holding one or more certificates to verify the peer with.
     * If empty - don't verifying the peer's certificate.
     */
    // 'CertificatesFilePath' => '',

    /**
     * [INTERNAL USAGE ONLY]
     * Switch debug mode: log all request and response data
     */
    // 'DebugMode' => false,

    /**
     * Set the logging class if DebugMode is enabled
     */
    // 'LogClass' => 'MangoPay\Libraries\Logs',

    /**
     * Set the cURL connection timeout limit (in seconds)
     */
    // 'CurlConnectionTimeout' => 30,

    /**
     * Set the cURL response timeout limit (in seconds)
     */
    // 'CurlResponseTimeout' => 80,

    /*
     * Defined here as SDK donot have these values (SOLETRADER is missing)
     */
    'legal_user_type' => array(
      "BUSINESS" => "BUSINESS",
      //"ORGANIZATION" => "ORGANIZATION", // Not Required as per shared design
      "SOLETRADER" => "SOLETRADER",
    ),

    'kyc_users_image' => array(
        "BUSINESS" => array(
            'image_passport' => array(
                'label'             => 'PassPort',
                'label_mangopay'    => 'IDENTITY_PROOF',
            ),
            'image_registration' => array(
                'label'             => 'Registration Proof',
                'label_mangopay'    => 'REGISTRATION_PROOF',
            ),
            'image_association' => array(
                'label'             => 'Articles of association',
                'label_mangopay'    => 'ARTICLES_OF_ASSOCIATION',
            ),
            'image_shareholder' => array(
                'label'             => 'ShareHolder Declaration',
                'label_mangopay'    => 'SHAREHOLDER_DECLARATION',
            ),
        ),
        "SOLETRADER" => array(
            'image_passport' => array(
                'label'             => 'PassPort',
                'label_mangopay'    => 'IDENTITY_PROOF',
            ),
            'image_registration' => array(
                'label'             => 'Registration Proof',
                'label_mangopay'    => 'REGISTRATION_PROOF',
            ),
        )
    ),
    
    'kyc_document_status' => array(
        'CREATED'           => 'CREATED',
        'VALIDATION_ASKED'  => 'VALIDATION_ASKED',
        'VALIDATED'         => 'VALIDATED',
        'REFUSED'           => 'REFUSED',
    ),

];
