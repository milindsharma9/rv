<?php

return [
    //user_roles
    'admin_role_id' => '1',
    'user_role_id' => '3',
    'vendor_role_id' => '2',
    'driver_role_id' => '4',
	'paginatorvalue' =>50,
    //Curreny
    'currency_sign' => '£',
    //Query Condition
    'available_order_condition' => '1',
    'order_complete_condition' => '3',
    
    //Payment
    'nationality' => 'GB',
    'residence' => 'GB',
    'currency' => 'GBP',
    'fee' => '0', // currently we are deducting fee offline.
    'poundToPence' => 100,
    //
    'driver_charge' => '3.00',
    'estimated_delivery_time' => 20, // in mins
    
    'country_map_mp' => array(
      "GB" => "GB",
      "FR" => "FR",
      "US" => "US",
      "IN" => "IN",
    ),
    'special_categories' => array(32, 56, 57),
    
    //footer links
    
    
    
    //RDA/GDA constant
    'energy' => 1857,
    'fat' => 70,
    'sat_fat' => 20,
    'sugar' => 70,
    'salt' => 6.2,

    'product_desc_default_length' => 33,
    
    //RelatedOccasion & relatedProducts
    'related_occasion_count' => 3,
    'related_products_count' => 5,
    
    //serve
    'serve' => 'serves',
    
    'store_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
    'site_timings' => ['00:00-00:30' => '00:00 - 00:30', '00:30-01:00' => '00:30 - 01:00', '01:00-01:30' => '01:00 - 01:30',
                        '01:30-02:00' => '01:30 - 02:00', '02:00-02:30' => '02:00 - 02:30', '02:30-03:00' => '02:30 - 03:00',
                        '03:00-03:30' => '03:00 - 03:30', '03:30-04:00' => '03:30 - 04:00', '04:00-04:30' => '04:00 - 04:30',
                        '04:30-05:00' => '04:30 - 05:00', '05:00-05:30' => '05:00 - 05:30', '05:30-06:00' => '05:30 - 06:00', 
                        '06:00-06:30' => '06:00 - 06:30', '06:30-07:00' => '06:30 - 07:00', '07:00-07:30' => '07:00 - 07:30',
                        '07:30-08:00' => '07:30 - 08:00', '08:00-08:30' => '08:00 - 08:30', '08:30-09:00' => '08:30 - 09:00',
                        '09:00-09:30' => '09:00 - 09:30', '09:30-10:00' => '09:30 - 10:00', '10:00-10:30' => '10:00 - 10:30',
                        '10:30-11:00' => '10:30 - 11:00', '11:00-11:30' => '11:00 - 11:30', '11:30-12:00' => '11:30 - 12:00',
                        '12:00-12:30' => '12:00 - 12:30', '12:30-13:00' => '12:30 - 13:00', '13:00-13:30' => '13:00 - 13:30',
                        '13:30-14:00' => '13:30 - 14:00', '14:00-14:30' => '14:00 - 14:30', '14:30-15:00' => '14:30 - 15:00',
                        '15:00-15:30' => '15:00 - 15:30', '15:30-16:00' => '15:30 - 16:00', '16:00-16:30' => '16:00 - 16:30', 
                        '16:30-17:00' => '16:30 - 17:00', '17:00-17:30' => '17:00 - 17:30', '17:30-18:00' => '17:30 - 18:00',
                        '18:00-18:30' => '18:00 - 18:30', '18:30-19:00' => '18:30 - 19:00', '19:00-19:30' => '19:00 - 19:30',
                        '19:30-20:00' => '19:30 - 20:00', '20:00-20:30' => '20:00 - 20:30', '20:30-21:00' => '20:30 - 21:00',
                        '21:00-21:30' => '21:00 - 21:30', '21:30-22:00' => '21:30 - 22:00', '22:00-22:30' => '22:00 - 22:30',
                        '22:30-23:00' => '22:30 - 23:00', '23:00-23:30' => '23:00 - 23:30', '23:30-23:59' => '23:30 - 23:59'],
    'store_timings' => ['00:00:01' => '00:00', '00:30:00' => '00:30', '01:00:00' => '01:00',
                        '01:30:00' => '01:30', '02:00:00' => '02:00', '02:30:00' => '02:30',
                        '03:00:00' => '03:00', '03:30:00' => '03:30', '04:00:00' => '04:00',
                        '04:30:00' => '04:30', '05:00:00' => '05:00', '05:30:00' => '05:30', 
                        '06:00:00' => '06:00', '06:30:00' => '06:30', '07:00:00' => '07:00',
                        '07:30:00' => '07:30', '08:00:00' => '08:00', '08:30:00' => '08:30',
                        '09:00:00' => '09:00', '09:30:00' => '09:30', '10:00:00' => '10:00',
                        '10:30:00' => '10:30', '11:00:00' => '11:00', '11:30:00' => '11:30',
                        '12:00:00' => '12:00', '12:30:00' => '12:30', '13:00:00' => '13:00',
                        '13:30:00' => '13:30', '14:00:00' => '14:00', '14:30:00' => '14:30',
                        '15:00:00' => '15:00', '15:30:00' => '15:30', '16:00:00' => '16:00', 
                        '16:30:00' => '16:30', '17:00:00' => '17:00', '17:30:00' => '17:30',
                        '18:00:00' => '18:00', '18:30:00' => '18:30', '19:00:00' => '19:00',
                        '19:30:00' => '19:30', '20:00:00' => '20:00', '20:30:00' => '20:30',
                        '21:00:00' => '21:00', '21:30:00' => '21:30', '22:00:00' => '22:00',
                        '22:30:00' => '22:30', '23:00:00' => '23:00', '23:30:00' => '23:30',
                        '23:59:00' => '23:59'],
    
    /*
     * Cart Postcode keys
     */
    'user_delivery_postcode_session_key'    => 'user_delivery_postcode',
    'user_delivery_lat_session_key'         => 'user_delivery_lat',
    'user_delivery_lng_session_key'         => 'user_delivery_lng',
    
    /*
     * Landing Page Postcode keys
     */
    'user_landing_postcode_session_key'     => 'user_landing_postcode',
    'user_landing_lat_session_key'          => 'user_landing_lat',
    'user_landing_lng_session_key'          => 'user_landing_lng',
    
    'user_cart_unique_session_key'          => 'user_cart_unique_key',
    
    'bank_detail_types' => array(
      "IBAN" => "IBAN",
      "US" => "US",
      "GB" => "GB",
      "CA" => "CA",
      "OTHER" => "OTHER",
    ),
    
    /*
     * Country Select Array
     */
    "country_select" => array(
        "AF" => "AFGHANISTAN",
        "AL" => "ALBANIA",
        "DZ" => "ALGERIA",
        "AS" => "AMERICAN SAMOA",
        "AD" => "ANDORRA",
        "AO" => "ANGOLA",
        "AI" => "ANGUILLA",
        "AQ" => "ANTARCTICA",
        "AG" => "ANTIGUA AND BARBUDA",
        "AR" => "ARGENTINA",
        "AM" => "ARMENIA",
        "AW" => "ARUBA",
        "AU" => "AUSTRALIA",
        "AT" => "AUSTRIA",
        "AZ" => "AZERBAIJAN",
        "BS" => "BAHAMAS",
        "BH" => "BAHRAIN",
        "BD" => "BANGLADESH",
        "BB" => "BARBADOS",
        "BY" => "BELARUS",
        "BE" => "BELGIUM",
        "BZ" => "BELIZE",
        "BJ" => "BENIN",
        "BM" => "BERMUDA",
        "BT" => "BHUTAN",
        "BO" => "BOLIVIA",
        "BA" => "BOSNIA AND HERZEGOVINA",
        "BW" => "BOTSWANA",
        "BV" => "BOUVET ISLAND",
        "BR" => "BRAZIL",
        "IO" => "BRITISH INDIAN OCEAN TERRITORY",
        "BN" => "BRUNEI DARUSSALAM",
        "BG" => "BULGARIA",
        "BF" => "BURKINA FASO",
        "BI" => "BURUNDI",
        "KH" => "CAMBODIA",
        "CM" => "CAMEROON",
        "CA" => "CANADA",
        "CV" => "CAPE VERDE",
        "KY" => "CAYMAN ISLANDS",
        "CF" => "CENTRAL AFRICAN REPUBLIC",
        "TD" => "CHAD",
        "CL" => "CHILE",
        "CN" => "CHINA",
        "CX" => "CHRISTMAS ISLAND",
        "CC" => "COCOS (KEELING) ISLANDS",
        "CO" => "COLOMBIA",
        "KM" => "COMOROS",
        "CG" => "CONGO",
        "CD" => "CONGO, THE DEMOCRATIC REPUBLIC OF THE",
        "CK" => "COOK ISLANDS",
        "CR" => "COSTA RICA",
        "CI" => "COTE D IVOIRE",
        "HR" => "CROATIA",
        "CU" => "CUBA",
        "CY" => "CYPRUS",
        "CZ" => "CZECH REPUBLIC",
        "DK" => "DENMARK",
        "DJ" => "DJIBOUTI",
        "DM" => "DOMINICA",
        "DO" => "DOMINICAN REPUBLIC",
        "TP" => "EAST TIMOR",
        "EC" => "ECUADOR",
        "EG" => "EGYPT",
        "SV" => "EL SALVADOR",
        "GQ" => "EQUATORIAL GUINEA",
        "ER" => "ERITREA",
        "EE" => "ESTONIA",
        "ET" => "ETHIOPIA",
        "FK" => "FALKLAND ISLANDS (MALVINAS)",
        "FO" => "FAROE ISLANDS",
        "FJ" => "FIJI",
        "FI" => "FINLAND",
        "FR" => "FRANCE",
        "GF" => "FRENCH GUIANA",
        "PF" => "FRENCH POLYNESIA",
        "TF" => "FRENCH SOUTHERN TERRITORIES",
        "GA" => "GABON",
        "GM" => "GAMBIA",
        "GE" => "GEORGIA",
        "DE" => "GERMANY",
        "GH" => "GHANA",
        "GI" => "GIBRALTAR",
        "GR" => "GREECE",
        "GL" => "GREENLAND",
        "GD" => "GRENADA",
        "GP" => "GUADELOUPE",
        "GU" => "GUAM",
        "GT" => "GUATEMALA",
        "GN" => "GUINEA",
        "GW" => "GUINEA-BISSAU",
        "GY" => "GUYANA",
        "HT" => "HAITI",
        "HM" => "HEARD ISLAND AND MCDONALD ISLANDS",
        "VA" => "HOLY SEE (VATICAN CITY STATE)",
        "HN" => "HONDURAS",
        "HK" => "HONG KONG",
        "HU" => "HUNGARY",
        "IS" => "ICELAND",
        "IN" => "INDIA",
        "ID" => "INDONESIA",
        "IR" => "IRAN, ISLAMIC REPUBLIC OF",
        "IQ" => "IRAQ",
        "IE" => "IRELAND",
        "IL" => "ISRAEL",
        "IT" => "ITALY",
        "JM" => "JAMAICA",
        "JP" => "JAPAN",
        "JO" => "JORDAN",
        "KZ" => "KAZAKSTAN",
        "KE" => "KENYA",
        "KI" => "KIRIBATI",
        "KP" => "KOREA DEMOCRATIC PEOPLES REPUBLIC OF",
        "KR" => "KOREA REPUBLIC OF",
        "KW" => "KUWAIT",
        "KG" => "KYRGYZSTAN",
        "LA" => "LAO PEOPLES DEMOCRATIC REPUBLIC",
        "LV" => "LATVIA",
        "LB" => "LEBANON",
        "LS" => "LESOTHO",
        "LR" => "LIBERIA",
        "LY" => "LIBYAN ARAB JAMAHIRIYA",
        "LI" => "LIECHTENSTEIN",
        "LT" => "LITHUANIA",
        "LU" => "LUXEMBOURG",
        "MO" => "MACAU",
        "MK" => "MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF",
        "MG" => "MADAGASCAR",
        "MW" => "MALAWI",
        "MY" => "MALAYSIA",
        "MV" => "MALDIVES",
        "ML" => "MALI",
        "MT" => "MALTA",
        "MH" => "MARSHALL ISLANDS",
        "MQ" => "MARTINIQUE",
        "MR" => "MAURITANIA",
        "MU" => "MAURITIUS",
        "YT" => "MAYOTTE",
        "MX" => "MEXICO",
        "FM" => "MICRONESIA, FEDERATED STATES OF",
        "MD" => "MOLDOVA, REPUBLIC OF",
        "MC" => "MONACO",
        "MN" => "MONGOLIA",
        "MS" => "MONTSERRAT",
        "MA" => "MOROCCO",
        "MZ" => "MOZAMBIQUE",
        "MM" => "MYANMAR",
        "NA" => "NAMIBIA",
        "NR" => "NAURU",
        "NP" => "NEPAL",
        "NL" => "NETHERLANDS",
        "AN" => "NETHERLANDS ANTILLES",
        "NC" => "NEW CALEDONIA",
        "NZ" => "NEW ZEALAND",
        "NI" => "NICARAGUA",
        "NE" => "NIGER",
        "NG" => "NIGERIA",
        "NU" => "NIUE",
        "NF" => "NORFOLK ISLAND",
        "MP" => "NORTHERN MARIANA ISLANDS",
        "NO" => "NORWAY",
        "OM" => "OMAN",
        "PK" => "PAKISTAN",
        "PW" => "PALAU",
        "PS" => "PALESTINIAN TERRITORY, OCCUPIED",
        "PA" => "PANAMA",
        "PG" => "PAPUA NEW GUINEA",
        "PY" => "PARAGUAY",
        "PE" => "PERU",
        "PH" => "PHILIPPINES",
        "PN" => "PITCAIRN",
        "PL" => "POLAND",
        "PT" => "PORTUGAL",
        "PR" => "PUERTO RICO",
        "QA" => "QATAR",
        "RE" => "REUNION",
        "RO" => "ROMANIA",
        "RU" => "RUSSIAN FEDERATION",
        "RW" => "RWANDA",
        "SH" => "SAINT HELENA",
        "KN" => "SAINT KITTS AND NEVIS",
        "LC" => "SAINT LUCIA",
        "PM" => "SAINT PIERRE AND MIQUELON",
        "VC" => "SAINT VINCENT AND THE GRENADINES",
        "WS" => "SAMOA",
        "SM" => "SAN MARINO",
        "ST" => "SAO TOME AND PRINCIPE",
        "SA" => "SAUDI ARABIA",
        "SN" => "SENEGAL",
        "SC" => "SEYCHELLES",
        "SL" => "SIERRA LEONE",
        "SG" => "SINGAPORE",
        "SK" => "SLOVAKIA",
        "SI" => "SLOVENIA",
        "SB" => "SOLOMON ISLANDS",
        "SO" => "SOMALIA",
        "ZA" => "SOUTH AFRICA",
        "GS" => "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS",
        "ES" => "SPAIN",
        "LK" => "SRI LANKA",
        "SD" => "SUDAN",
        "SR" => "SURINAME",
        "SJ" => "SVALBARD AND JAN MAYEN",
        "SZ" => "SWAZILAND",
        "SE" => "SWEDEN",
        "CH" => "SWITZERLAND",
        "SY" => "SYRIAN ARAB REPUBLIC",
        "TW" => "TAIWAN, PROVINCE OF CHINA",
        "TJ" => "TAJIKISTAN",
        "TZ" => "TANZANIA, UNITED REPUBLIC OF",
        "TH" => "THAILAND",
        "TG" => "TOGO",
        "TK" => "TOKELAU",
        "TO" => "TONGA",
        "TT" => "TRINIDAD AND TOBAGO",
        "TN" => "TUNISIA",
        "TR" => "TURKEY",
        "TM" => "TURKMENISTAN",
        "TC" => "TURKS AND CAICOS ISLANDS",
        "TV" => "TUVALU",
        "UG" => "UGANDA",
        "UA" => "UKRAINE",
        "AE" => "UNITED ARAB EMIRATES",
        "GB" => "UNITED KINGDOM",
        "US" => "UNITED STATES",
        "UM" => "UNITED STATES MINOR OUTLYING ISLANDS",
        "UY" => "URUGUAY",
        "UZ" => "UZBEKISTAN",
        "VU" => "VANUATU",
        "VE" => "VENEZUELA",
        "VN" => "VIET NAM",
        "VG" => "VIRGIN ISLANDS, BRITISH",
        "VI" => "VIRGIN ISLANDS, U.S.",
        "WF" => "WALLIS AND FUTUNA",
        "EH" => "WESTERN SAHARA",
        "YE" => "YEMEN",
        "YU" => "YUGOSLAVIA",
        "ZM" => "ZAMBIA",
        "ZW" => "ZIMBABWE",
        ),
    
    'phone_validate_default_country'    => 'GB',
    
    
    'refund_tat'                        => '5',
    
    'store_default_country'             => 'GB',
    'vendor_store_default_email_suffix' => '_vendor_custom@test.com',
    
    'wine_cat_ids'                      => [15, 37, 38, 39, 40, 41, 64],
    'beer_cat_ids'                      => [16, 18, 19, 20, 21, 36, 47],
    'spirit_cat_ids'                    => [17, 42, 43, 44, 45, 46, 48],
    'drinks_cat_ids'                    => [12, 24, 25, 26, 27, 59, 63, 60, 65, 61, 62],
    'food_cat_ids'                      => [13, 30, 52, 53, 54, 55, 31, 49, 22, 28, 29],
    'other_cat_ids'                     => [14, 32, 33, 34, 56, 57, 50, 51, 58],
    
    'cat_not_allowed'                   => [11],
    
    'contact_address'                   => 'test',
    'contact_service'                   => '020 7499 2842',
    'contact_email'                     => 'test@test.com',

];

