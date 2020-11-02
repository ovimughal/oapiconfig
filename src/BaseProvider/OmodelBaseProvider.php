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
                    $updatedResult = $this->queryResultCheck($queryResult, $errMsg);
                    $result = ['updated' => is_array($updatedResult) ? count($updatedResult) : $updatedResult];
                    break;
                case 'delete':
                    $deletedResult = $this->queryResultCheck($queryResult, $errMsg);
                    $result = ['deleted' => is_array($deletedResult) ? count($deletedResult) : $deletedResult];
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

    public function nativeSelect($sql, $errMsg = null, $paramsArr = [], $option = null)
    {
        try {
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');
            //print_r($paramsArr);die();

            $pStmt = $this->getDoctObjMngr()->getConnection()->prepare($sql);
            $pStmt->execute($paramsArr);

            switch ($option) {
                case 'update':
                    $queryResult = $pStmt->rowCount();
                    $updatedResult = $this->queryResultCheck($queryResult, $errMsg);
                    $result = ['updated' => is_array($updatedResult) ? count($updatedResult) : $updatedResult];
                    break;
                case 'delete':
                    $queryResult = $pStmt->rowCount();
                    $deletedResult = $this->queryResultCheck($queryResult, $errMsg);
                    $result = ['deleted' => is_array($deletedResult) ? count($deletedResult) : $deletedResult];
                    break;
                default:
                    $queryResult = $pStmt->fetchAll();
                    $result = $this->queryResultCheck($queryResult, $errMsg);
                    break;
            }
            //            if (!count($paramsArr)) {
            //                $queryResult = $this->getDoctObjMngr()->getConnection()->fetchAll($sql);
            //            } else {
            //                $pStmt = $this->getDoctObjMngr()->getConnection()->prepare($sql);
            //                $pStmt->execute($paramsArr);
            //                $queryResult = $pStmt->fetchAll();
            //            }
            //            $result = $this->queryResultCheck($queryResult, $errMsg);
        } catch (Exception $exc) {
            parent::setSuccess(false);
            $result = $exc;
            throw new Exception($exc);
        }

        return $result;
    }

    public function nativeUpdate($sql, $errMsg = null, $paramsArr = [])
    {
        return $this->nativeSelect($sql, $errMsg, $paramsArr, 'update');
    }

    public function nativeDelete($sql, $errMsg = null, $paramsArr = [])
    {
        return $this->nativeSelect($sql, $errMsg, $paramsArr, 'delete');
    }

    public function generateJasperReport(
        string $sqlQuery,
        string $reportTemplate,
        array $parameters = [],
        array $subReportParameters = [],
        string $outputFormat = 'pdf',
        string $language = null,
        array $properties = [],
        bool $generateNew = false,
        bool $throwExc = false
    ) {
        try {
            //        $class_name = '\GlobalProcedure\Model\GlobalProcedureModel';
            //        if (class_exists($class_name)){
            //            $globalProcedure = new \GlobalProcedure\Model\GlobalProcedureModel();
            //            $userData = \Oapiconfig\DI\ServiceInjector::oJwtizer()->getUserInfo();
            //            $userAuthenticationCode = $userData['userId'];
            //            $userPrefrences = $globalProcedure->getUserPrefrences($userAuthenticationCode);
            //            $language = $userPrefrences['language'];
            //        }

            $language = $language ?? $this->languageScanner();

            require_once(__DIR__ . '/ReportingEngine/JasperEngine.php');
            $result = executeJasper(
                $sqlQuery,
                $reportTemplate,
                $parameters,
                $subReportParameters,
                $outputFormat,
                $language,
                $properties,
                $generateNew
            );
        } catch (Exception $exc) {
            if ($throwExc) {
                throw new Exception($exc);
            }

            $result = $exc;
        }
        
        return $result;
    }
}
