<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Factories;

use Interop\Container\ContainerInterface;
use Oapiconfig\Services\OormService;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
/**
 * Description of OormFactory
 *
 * @author OviMughal
 */
class OormFactory implements FactoryInterface
{    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //return new OormService($serviceLocator);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new OormService($container);
    }

}
