<?php

return [
    'driver_available_day' => ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday'],
    'driver_available_time' => ['1' => 'Day', '2' => 'Evening', '3' => 'Late Night', '4' => 'All'],
    'driver_occupation'     => [
        '1' => 'Full time', 
        '2' => 'Part time',
        '3' => 'Self-employed',
        '4' => 'Unemployed',
    ],
    
    'questions'             => [
                                '1' => 'Are you a competent Cyclist?',
                                '2' => 'How many miles would you currently cycle per week?',
                                '3' => 'Have you ever been convicted of a criminal offence?',
                                '4' => 'Do you have a motor bike or scotter?',
                                '5' => 'Do you have driver\'s license and CBT?',
                                '6' => 'Do you have a food delivery or courier Insurance?',
                                '7' => 'Do you have your own food delivery box?',
    ],
    //'cyclist_question'     => ['1', '2', '3'],
    //'scooter_question'     => ['4', '5', '6', '7', '3'],
    
    'bicycle_question'     => [
        '1' => 'Are you a fit and competent cyclist?*',
        '2' => 'How many miles would you currently cycle per week?*',
        '3' => 'Have you ever been convicted of a criminal offence?*',
    ],
    'scooter_question'     => [
        '5' => 'Do you have a motor bike or scooter?*',
        '6' => 'Do you have a driver\'s license and CBT?*',
        '7' => 'Do you have a food delivery or courier Insurance?*',
        '8' => 'Do you have your own food delivery box?',
        '4' => 'Have you ever been convicted of a criminal offence?*',
    ],
    'vehicle_type'     => [
        'bicycle' => 'BICYCLE',
        'scooter' => 'SCOOTER',
        
    ],
    
    'miles_per_week_select' => [
        '1-5' => '1-5',
        '6-10' => '6-10',
        '11-20' => '11-20',
        '20+' => '20+',
    ],
    
    'vehicle_type_bicycle' => 'bicycle',
    'vehicle_type_scooter' => 'scooter',

];

