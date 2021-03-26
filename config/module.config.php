<?php

namespace Oapiconfig;

use Oapiconfig\Controller\ConfigController;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

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
    'controller_plugins' => [
        'factories' => [
            Gateway\GateKeeper::class => InvokableFactory::class
        ],
        'aliases' => [
            'GateKeeper' => Gateway\GateKeeper::class
        ]
    ],
];
