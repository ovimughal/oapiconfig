<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

use Doctrine\ORM\EntityManager;
use Exception;
use Oapiconfig\DI\ServiceInjector;
use Oapiconfig\Services\OhydrationService;
use Oapiconfig\Services\OormService;

/**
 * Description of OmodelBaseProvider
 *
 * @author OviMughal
 */
class OmodelBaseProvider extends OhandlerBaseProvider
{

    private EntityManager $doctObjMngr;
    private $path;
    private OormService $oOrm;

    public function __construct()
    {
        parent::__construct();
        $this->oOrm = ServiceInjector::oOrm();
        // $this->setDoctObjMngr($oOrm);
        // $this->setPath($oOrm);
    }

    public function hydrateEntity(array $dataArr, object $entity, string $doctrineServiceName = 'doctObjMngr') : OhydrationService
    {
        $oOrm = ServiceInjector::oOrm();
        $hydrator = $oOrm->entityHydrator($dataArr, $entity, $doctrineServiceName);
        return $hydrator;
    }

    // public function setDoctObjMngr(OormService $oOrm)
    // {
    //     $this->doctObjMngr = $oOrm->getDoctObjMngr();
    // }

    public function getDoctObjMngr(string $doctrineServiceName = 'doctObjMngr') : EntityManager
    {
        return $this->oOrm->getDoctObjMngr($doctrineServiceName);
    }

    // public function setPath($oOrm)
    // {
    //     $this->path = $oOrm->getEntityPath();
    // }

    public function getPath(string $ormConfig = 'orm_default_path') : string
    {
        return $this->oOrm->getEntityPath($ormConfig);
    }

    public function getEntity(string $entityName, string $ormConfig) : object
    {
        $entity = '';
        eval('$entity = new ' . $this->getPath($ormConfig) . '\\' . $entityName . '();');

        return (object)$entity;
    }

    public function insert(array $dataArr, string $entityName, string $idName, string $doctrineServiceName = 'doctObjMngr', string $ormConfig = 'orm_default_path') : array
    {
        try {
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');

            $entityObj = $this->getEntity($entityName, $ormConfig);
            $this->hydrateEntity($dataArr, $entityObj, $doctrineServiceName);
            $this->getDoctObjMngr($doctrineServiceName)->persist($entityObj);
            $this->getDoctObjMngr($doctrineServiceName)->flush($entityObj);

            $lastInsertId = true;
            eval('$lastInsertId = $entityObj->get' . $idName . '();');

            $result = ['_id' => $lastInsertId]; //['_id' => $lastInsertId, 'msg' => 'success'];
        } catch (Exception $exc) {
            parent::setSuccess(false);
            throw new Exception($exc);
        }

        return $result;
    }

    public function select($dql, $paramsArr, $errMsg = null, $limit = null, $option = null, string $doctrineServiceName = 'doctObjMngr')
    {
        try {
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');

            $query = $this->getDoctObjMngr($doctrineServiceName)->createQuery($dql);
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

    public function update($dql, $paramsArr, $errMsg = null, string $doctrineServiceName = 'doctObjMngr')
    {
        return $this->select($dql, $paramsArr, $errMsg, null, 'update', $doctrineServiceName);
    }

    public function delete($dql, $paramsArr, $errMsg = null, string $doctrineServiceName = 'doctObjMngr')
    {
        return $this->select($dql, $paramsArr, $errMsg, null, 'delete', $doctrineServiceName);
    }

    public function deleteWithId($entityName, $id, string $doctrineServiceName = 'doctObjMngr', string $ormConfig = 'orm_default_path')
    {
        try {
            $entityObject = $this->getDoctObjMngr($doctrineServiceName)->find($this->getPath($ormConfig) . '\\' . $entityName, $id);
            $this->getDoctObjMngr($doctrineServiceName)->remove($entityObject);
            $this->getDoctObjMngr($doctrineServiceName)->flush();
            $result = ['deleted' => $id];
        } catch (Exception $exc) {
            parent::setSuccess(false);
            $result = $exc;
            throw new Exception($exc);
        }

        return $result;
    }

    public function nativeSelect($sql, $errMsg = null, $paramsArr = [], $option = null, string $doctrineServiceName = 'doctObjMngr')
    {
        try {
            parent::setSuccess(true);
            parent::setMsg('Executed Successfully');
            //print_r($paramsArr);die();

            $pStmt = $this->getDoctObjMngr($doctrineServiceName)->getConnection()->prepare($sql);
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

    public function nativeUpdate($sql, $errMsg = null, $paramsArr = [], string $doctrineServiceName = 'doctObjMngr')
    {
        return $this->nativeSelect($sql, $errMsg, $paramsArr, 'update', $doctrineServiceName);
    }

    public function nativeDelete($sql, $errMsg = null, $paramsArr = [], string $doctrineServiceName = 'doctObjMngr')
    {
        return $this->nativeSelect($sql, $errMsg, $paramsArr, 'delete', $doctrineServiceName);
    }

    public function generateJasperReport(
        string $sqlQuery,
        string $reportTemplate,
        array $parameters = [],
        array $subReportParameters = [],
        string $outputFormat = null,
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

            $outputFormat = $outputFormat ?? 'pdf';
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
