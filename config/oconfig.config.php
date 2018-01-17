<?php
namespace Oapiconfig;

return [
    'oconfig_manager' => [
        'settings' => [
            'enable_login' => false,
            'enable_db_acl' => true,
            'app_development_env' => getenv('APPLICATION_ENV') == 'production' ? false : true,
            'base_path' => 'public',
            '_file_path' => 'img/',
            'customeName1_file_path' => '',
            'image_server' => 'http://localhost:8092/',
            'employee_image_path' => 'img/empProfile/',
            'company_image_path' => 'img/companylogo/',
        ],
        'api' => [
            'api_key' => 'jIJMLFjW2Jr2Ko1JCO0Gpi8s8KgHdiGT37I6UI+RedqRTVU5p4bDgaB++3Zn9Y0ixUO0GpE5VJq9NlIT9LbM5Q==',
        ],
        'ojwt' => [
            'jwt_key' => 'jbCdemn+Dr9j3JHh9zMtv8+W8MUR90LwsRH3R1TZDspOTgmqtGvcsUdoczdBsAMYUxDFi+9lKtN1QN5Da9JUdg==', //base64_encode(openssl_random_pseudo_bytes(64]],
            'algo' => 'HS512',
            'server' => 'http://localhost:8096/',
            'iatOffset' => 10,
            'expOffset' => 3590 //+ above 10 = 3600 => 1 hr
        ],
        'entities' => [
            'path' => 'Application\Entity'
        ]
    ],
];
