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
            Services\OaclService::class => Factories\OaclFactory::class,
            //'OfileManager' => 'Oapiconfig\Factories\OfilemanagerFactory',
            Services\OfilemanagerService::class => Factories\OfilemanagerFactory::class,
            //'Olanguage' => 'Oapiconfig\Factories\OlanguageFactory',
            Services\OlanguageService::class => Factories\OlanguageFactory::class,
            //'OConfigHighjacker' => 'Oapiconfig\Factories\OConfigHighjackerFactory',
            Services\OConfigHighjackerService::class => Factories\OConfigHighjackerFactory::class,
            //'OEncryption' => 'Oapiconfig\Factories\OEncryptionFactory',
            Services\OEncryptionService::class => Factories\OEncryptionFactory::class,
            //'OTenant' => 'Oapiconfig\Factories\OTenantFactory',
            Services\OTenantService::class => Factories\OTenantFactory::class
        ],
        'aliases' => [
            // Register an alias for Services
            'Ojwtizer' => Services\OjwtizerService::class,
            'Ohydration' => Services\OhydrationService::class,
            'Oorm' => Services\OormService::class,
            'Oapisecurity' => Services\OapisecurityService::class,
            'Oimagecurler' => Services\OimagecurlerService::class,
            'Oacl' => Services\OaclService::class,
            'Ofilemanager' => Services\OfilemanagerService::class,
            'Olanguage' => Services\OlanguageService::class,
            'OConfigHighjacker' => Services\OConfigHighjackerService::class,
            'OEncryption' => Services\OEncryptionService::class,
            'OTenant' => Services\OTenantService::class
        ],
    ]
];
