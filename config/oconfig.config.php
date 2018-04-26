<?php
namespace Oapiconfig;

return [
    'oconfig_manager' => [
        'settings' => [
            'enable_login' => false,
            'enable_db_acl' => false,
            'app_development_env' => getenv('APPLICATION_ENV') == 'development' ? true : false,
            //Start-Custom Keys
            'attachmentPath' => 'public/img/upload',
            'customeName1_file_path' => '',
            // End-Custom Keys
            
            // Start-For Reporting Engine
            'java_bridge' => 'http://localhost:8090/JavaBridge/java/Java.inc',
            'dbms' => 'sqlsrv',//mysql
            'dbms_server' => '192.168.100.17:1433',
            'data_base_name' => 'SalesConMigrated',
            'data_base_user' => 'sa',
            'data_base_password' => 'ERPSalesCon',
            'reporting_templates' => 'public/reporting/templates',
            'reporting_output' => 'public/reporting/output',
            'output_file_name' => 'output',
            'output_file_download_route' => 'http://10.10.0.36:9005/testnew',
            // End-For Reporting Engine
            
            // Start-For File Data Engine
            'file_server' => 'http://localhost:9005/',
            'filePath' => 'public/img',
            'remoteFilePath' => 'img/',
            'customeName2_file_path' => 'img/customeName2/',
            // End-For File Data Engine
        ],
        'api' => [
            'api_key' => 'jIJMLFjW2Jr2Ko1JCO0Gpi8s8KgHdiGT37I6UI+RedqRTVU5p4bDgaB++3Zn9Y0ixUO0GpE5VJq9NlIT9LbM5Q==',
            'hyperlink_api_key_security_one' => 'pOnMl',
            'hyperlink_api_key_security_two' => 'uTsRq',
            'hyperlink_security_salt' => '10WtJoS',
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
