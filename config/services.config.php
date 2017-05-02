<?php
namespace Oapiconfig;

return [
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
    ]
];

