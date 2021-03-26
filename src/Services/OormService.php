<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

/**
 * Description of OormService
 *
 * @author OviMughal
 */
class OormService
{
    private ContainerInterface $serviceLocator;   
    
    public function __construct(ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() : ContainerInterface
    {
        return $this->serviceLocator;
    }
    
    public function entityHydrator(array $dataArr, object $entity, string $doctrineServiceName = 'doctObjMngr') : OhydrationService
    {
        /**
         * @var OhydrationService
         */
        $hydrator = $this->getServiceLocator()->get('Ohydration');
        $hydrator($dataArr, $entity, $doctrineServiceName);

        return $hydrator;
    }
    
    public function getDoctObjMngr(string $doctrineServiceName = 'doctObjMngr') : EntityManager
    {
        /**
         * @var EntityManager
         */
        $doctObjMngr = $this->getServiceLocator()->get($doctrineServiceName);
        return $doctObjMngr;
    }
    
    public function getEntityPath(string $ormConfig = 'orm_default_path') : string
    {
        $oconfig = $this->getServiceLocator()->get('config');
        $entities = $oconfig['oconfig_manager']['entities'];
        $path = $entities[$ormConfig];
        return $path;
    }
}
