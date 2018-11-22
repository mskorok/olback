<?php
/**
 * Read more on Config Files
 * @link http://phalcon-rest.redound.org/config_files.html
 */

return [

    'application' => [
        'title' => 'OLSET App REST',
        'description' => 'This repository provides the API documentation for the OLSET App.',
        'baseUri' => '/',
        'viewsDir' => __DIR__ . '/../views/',
        'reportUploadDir' => __DIR__ . '/../../public/report/',
        'reportUploadLink' => '/report/',
        'admin' => 37,
        'settings' => [
            'default' => [
                'group' => 10000
            ]
        ],
        'survey' => [
            'init' => '_IS_',
            'evaluation' => '_ES_',
            'aar' => '_AAR_',
            'CRS' => '_CS_',
            'VS' => '_VS_',
            'demographics' => '_DS_',
            'demographicsCount' => 7,
            'initCount' => 28,
            'realityCount' => 3,
            'visionCount' => 3,
            'evaluationCount' => 28,
            'aarCount' => 4
        ],
        'report' => [
            'bg' => [
                'white' => 'app-bc-w',
                'red' => 'app-bc-c0',
                'blue' => 'app-bc-2a',
            ],
            'char' => [
                'white' => 'app-color-white'
            ],

            'dir' =>  __DIR__ . '/../views/report/'
        ]
    ],

    'authentication' => [
        'secret' => 'Someverydifficultsecret',
        'expirationTime' => 86400 * 7, // One week till token expires
    ],
];
