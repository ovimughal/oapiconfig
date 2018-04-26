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
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');

            $entityObj = $this->getEntity($entityName);
            $this->hydrateEntity($dataArr, $entityObj);
            $this->getDoctObjMngr()->persist($entityObj);
            $this->getDoctObjMngr()->flush($entityObj);

            $lastInsertId = true;
            eval('$lastInsertId = $entityObj->get' . $idName . '();');

            $result = ['_id' => $lastInsertId]; //['_id' => $lastInsertId, 'msg' => 'success'];
        } catch (Exception $exc) {
            parent::setSuccess(false);
            $result = $exc;
            throw new Exception($exc);
        }

        return $result;
    }

    public function select($dql, $paramsArr, $errMsg = null, $limit = null, $option = null)
    {
        try {
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');

            $query = $this->getDoctObjMngr()->createQuery($dql);
            $query->setParameters($paramsArr);
            if (null != $limit) {
                $query->setMaxResults($limit);
            }
            $queryResult = $query->getArrayResult();

            switch ($option) {
                case 'update':
                    $result = ['updated' => $this->queryResultCheck($queryResult, $errMsg)];
                    break;
                case 'delete':
                    $result = ['deleted' => $this->queryResultCheck($queryResult, $errMsg)];
                    break;
                default:
                    $result = $this->queryResultCheck($queryResult, $errMsg);
                    break;
            }
        } catch (Exception $exc) {
            parent::setSuccess(false);
            $result = $exc;
            throw new Exception($exc);
        }

        return $result;
    }

    public function queryResultCheck($queryResult, $errMsg = null)
    {
        $count = is_array($queryResult) ? count($queryResult) : $queryResult;
        if (0 == $count) {
            $msg = null == $errMsg ? 'Result Not Found' : $errMsg;

            /* commented, since return result is empty but the query is 
             * still valid. It is just it returns empty so success can't be false.
             */
            // $this->setSuccess(false);           
            parent::setMsg($msg);
            $queryResult = [];
        }

        return $queryResult;
    }

    public function update($dql, $paramsArr, $errMsg = null)
    {
        return $this->select($dql, $paramsArr, $errMsg, null, 'update');
    }

    public function delete($dql, $paramsArr, $errMsg = null)
    {
        return $this->select($dql, $paramsArr, $errMsg, null, 'delete');
    }

    public function deleteWithId($entityName, $id)
    {
        try {
            $entityObject = $this->getDoctObjMngr()->find($this->getPath() . '\\' . $entityName, $id);
            $this->getDoctObjMngr()->remove($entityObject);
            $this->getDoctObjMngr()->flush();
            $result = ['deleted' => $id];
        } catch (Exception $exc) {
            parent::setSuccess(false);
            $result = $exc;
            throw new Exception($exc);
        }

        return $result;
    }
    
    public function nativeSelect($sql, $errMsg = null)
    {
        try {
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');
            
            $queryResult = $this->getDoctObjMngr()->getConnection()->fetchAll($sql);
            $result = $this->queryResultCheck($queryResult, $errMsg);
        } catch (Exception $exc) {
            parent::setSuccess(false);
            $result = $exc;
            throw new Exception($exc);
        }

        return $result;
    }

    public function generateJasperReport($sqlQuery, $reportTemplate, $parameters = [], $outputFormat = 'pdf')
    {
        require __DIR__ . '/ReportingEngine/JasperEngine.php';
        return executeJasper($sqlQuery, $reportTemplate, $parameters, $outputFormat);
    }

}
