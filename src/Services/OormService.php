<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

/**
 * Description of OormService
 *
 * @author OviMughal
 */
class OormService
{
    private $serviceLocator;   
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator(){
        return $this->serviceLocator;
    }
    
    public function entityHydrator($dataArr, $entity)
    {
        $hydrator = $this->getServiceLocator()->get('Ohydration');
        $hydrator($dataArr, $entity);

        return $hydrator;
    }
    
    public function getDoctObjMngr()
    {
        $doctObjMngr = $this->getServiceLocator()->get('doctObjMngr');
        return $doctObjMngr;
    }
    
    public function getEntityPath()
    {
        $oconfig = $this->getServiceLocator()->get('config');
        $entities = $oconfig['oconfig_manager']['entities'];
        $path = $entities['path'];
        return $path;
    }
}
