<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Factories;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Oapiconfig\Services\OConfigHighjackerService;
/**
 * Description of OConfigHighjackerFactory
 *
 * @author OviMughal
 */
class OConfigHighjackerFactory implements FactoryInterface
{    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : OConfigHighjackerService
    {
        return new OConfigHighjackerService($container);
    }

}
