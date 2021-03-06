<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Factories;

use Interop\Container\ContainerInterface;
use Oapiconfig\Services\OjwtizerService;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Description of OjwtizerFactory
 *
 * @author OviMughal
 */
class OjwtizerFactory implements FactoryInterface
{    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //return new OjwtizerService($serviceLocator, $this->getConfiguration($serviceLocator));
    }
    
    public function getConfiguration(ContainerInterface $serviceLocator)
    {
        $oconfig = $serviceLocator->get('config');
        return $oconfig['oconfig_manager']['ojwt'];
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : OjwtizerService
    {
        return new OjwtizerService($container, $this->getConfiguration($container));
    }

}
