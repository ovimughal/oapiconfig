<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Factories;

use Interop\Container\ContainerInterface;
use Oapiconfig\Services\OhydrationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of OhydrationFactory
 *
 * @author OviMughal
 */
class OhydrationFactory implements FactoryInterface
{    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //return new OhydrationService($serviceLocator);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new OhydrationService($container);
    }

}
