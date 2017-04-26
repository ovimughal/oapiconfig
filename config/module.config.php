<?php

namespace Oapiconfig;

use Oapiconfig\Controller\ConfigController;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            ConfigController::class => InvokableFactory::class
        ],
    ],
    'router' => [
        'routes' => [
            'oapiconfig' => [
                'type' => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route' => '/api/config',
                    'defaults' => [
                        'controller' => ConfigController::class,
                    //'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Oapiconfig' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'service_manager' => [
        'factories' => [
            //'Ojwtizer' => 'Oapiconfig\Factories\OjwtizerFactory',
            Services\OjwtizerService::class => Factories\OjwtizerFactory::class,
            //'Ohydration' => 'Oapiconfig\Factories\OhydrationFactory',
            Services\OhydrationService::class => Factories\OhydrationFactory::class,
            //'Oorm' => 'Oapiconfig\Factories\OormFactory',
            Services\OormService::class => Factories\OormFactory::class,
            //'Ovalidate' => 'Oapiconfig\Factories\OvalidationFactory',            
            //'Oapisecurity' => 'Oapiconfig\Factories\OapisecurityFactory',
            Services\OapisecurityService::class => Factories\OapisecurityFactory::class,
            //'Oimagecurler' => 'Oapiconfig\Factories\OimagecurlerFactory',
            Services\OimagecurlerService::class => Factories\OimagecurlerFactory::class,
            //'Oacl' => 'Oapiconfig\Factories\OaclFactory',
            Services\OaclService::class => Factories\OaclFactory::class
        ],
        'aliases' => [
            // Register an alias for Services
            'Ojwtizer' => Services\OjwtizerService::class,
            'Ohydration' => Services\OhydrationService::class,
            'Oorm' => Services\OormService::class,
            'Oapisecurity' => Services\OapisecurityService::class,
            'Oimagecurler' => Services\OimagecurlerService::class,
            'Oacl' => Services\OaclService::class,
        ],
    ],
    'oconfig_manager' => [
        'settings' => [
            'image_server' => 'http://salesconsghira:8092/',
            'employee_image_path' => 'img/empProfile/',
            'company_image_path' => 'img/companylogo/',
        ],
        'api' => [
            'api_key' => 'jIJMLFjW2Jr2Ko1JCO0Gpi8s8KgHdiGT37I6UI+RedqRTVU5p4bDgaB++3Zn9Y0ixUO0GpE5VJq9NlIT9LbM5Q==',
        ],
        'ojwt' => [
            'jwt_key' => 'jbCdemn+Dr9j3JHh9zMtv8+W8MUR90LwsRH3R1TZDspOTgmqtGvcsUdoczdBsAMYUxDFi+9lKtN1QN5Da9JUdg==', //base64_encode(openssl_random_pseudo_bytes(64]],
            'algo' => 'HS512',
            'server' => 'http://salesconsghira:8096/',
            'iatOffset' => 10,
            'expOffset' => 3590 //+ above 10 = 3600 => 1 hr
        ],
        'entities' => [
            'path' => 'Application\Entity'
        ]
    ],
    'controller_plugins' => [
        'factories' => [
            Gateway\GateKeeper::class => InvokableFactory::class
        ],
        'aliases' => [
            'GateKeeper' => Gateway\GateKeeper::class
        ]
    ],
];
