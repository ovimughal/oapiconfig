<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

use Exception;
use Oapiconfig\DI\ServiceInjector;

/**
 * Description of OmodelBaseProvider
 *
 * @author OviMughal
 */
class OmodelBaseProvider extends OhandlerBaseProvider
{

    private $doctObjMngr;
    private $path;

    public function __construct()
    {
        parent::__construct();
        $oOrm = ServiceInjector::oOrm();
        $this->setDoctObjMngr($oOrm);
        $this->setPath($oOrm);
    }

    public function hydrateEntity($dataArr, $entity)
    {
        $oOrm = ServiceInjector::oOrm();
        $hydrator = $oOrm->entityHydrator($dataArr, $entity);
        return $hydrator;
    }

    public function setDoctObjMngr($oOrm)
    {
        $this->doctObjMngr = $oOrm->getDoctObjMngr();
    }

    public function getDoctObjMngr()
    {
        return $this->doctObjMngr;
    }

    public function setPath($oOrm)
    {
        $this->path = $oOrm->getEntityPath();
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getEntity($entityName)
    {
        $entity = '';
        eval('$entity = new ' . $this->getPath() . '\\' . $entityName . '();');

        return $entity;
    }

    public function insert($dataArr, $entityName, $idName)
    {
        try {
            $entityObj = $this->getEntity($entityName);
            $this->hydrateEntity($dataArr, $entityObj);
            $this->getDoctObjMngr()->persist($entityObj);
            $this->getDoctObjMngr()->flush($entityObj);

            $lastInsertId = true;
            eval('$lastInsertId = $entityObj->get' . $idName . '();');

            $result = ['_id' => $lastInsertId]; //['_id' => $lastInsertId, 'msg' => 'success'];
        } catch (Exception $exc) {
            $this->setSuccess(false);
            $result = $exc;
        }

        return $result;
    }

    public function select($dql, $paramsArr, $errMsg = null)
    {
        try {
            $query = $this->getDoctObjMngr()->createQuery($dql);
            $query->setParameters($paramsArr);
            $queryResult = $query->getArrayResult();
            $result = $this->queryResultCheck($queryResult, $errMsg);
        } catch (Exception $exc) {
            $this->setSuccess(false);
            $result = $exc;
        }
        
        return $result;
    }

    public function queryResultCheck($queryResult,$errMsg = null)
    {
        if (!count($queryResult)) {
            $msg = null == $errMsg ? 'Result Not Found' : $errMsg;
            $this->setSuccess(false);
            $this->setMsg($msg);
            $queryResult = [];
        }

        return $queryResult;
    }

}
