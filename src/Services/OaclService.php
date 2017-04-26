<?php

namespace Oapiconfig\Services;

use Exception;
use Oapiconfig\BaseProvider\OmodelBaseProvider;
use Oapiconfig\DI\ServiceInjector;
use Oapiconfig\Sniffers\OexceptionSniffer;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\View\Model\JsonModel;

class OaclService extends OmodelBaseProvider
{

    private $role;

    public function __construct()
    {
        parent::__construct();
        $userInfo = ServiceInjector::oJwtizer()->getUserInfo();
        $this->setRole($userInfo['role']);
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return isset($this->role) ? $this->role : 'Admin';
    }

    public function getUserRoles()
    {
        $dql = 'SELECT u.userrolename FROM '
                . $this->getPath() . '\Userrole u WHERE u.hasdeleted=?1';
        $params = [1 => 0];
        $errMsg = 'User Roles Not Defined';
        $result = $this->select($dql, $params, $errMsg);

        return $result;
    }

    public function resourceDump()
    {
        try {
            $acl = new Acl();
            $acl->deny();

            $result = $this->getUserRoles();
            if (!is_a($result, 'Exception')) {
                foreach ($result as $r):
                    $acl->addRole(new Role($r['userrolename']));
                endforeach;
                $acl->addRole(new Role('SuperAdmin'));

                $acl->addResource('doctrineormmodule');
                $acl->addResource('oapigps');
                $acl->addResource('oapiemployeeprofile');
                $acl->addResource('oapiemployeeattendance');
                $acl->addResource('oapimastersettings');
                $acl->addResource('oapisalesorder');

                $acl->allow('Admin', 'oapiemployeeprofile', 'employeeprofile:GET');

                $acl->allow('Admin', 'oapigps', 'gps:POST');
                $acl->allow('Admin', 'oapigps', 'allemployeegps:GET');
                $acl->allow('Admin', 'oapigps', 'singleemployeegps:GET');

                $acl->allow('Admin', 'oapiemployeeattendance', 'employeeattendance:POST');
                
                $acl->allow('Admin', 'oapimastersettings', 'mastersettings:GET');
                
                //$acl->allow('Admin', 'oapisalesorder', 'salesorder:GET');
            }
            else {
                throw new Exception($result->getMessage(), $result->getCode(), $result->getPrevious());
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode(), $exc->getPrevious());
        }

        return $acl;
    }

    public function authorizationCheck($e)
    {
        $res = $e->getResponse();
        $allowed = true;
        try {
            $controllerTarget = $e->getTarget();
            $controllerClass = get_class($controllerTarget);
            $moduleName = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
            $role = $this->getRole();
            $routeMatch = $e->getRouteMatch();

            $restMethod = $e->getRequest()->getMethod();
            $controllerName = $routeMatch->getParam('controller', 'not-found');
            $exploded_arr = explode('\\', $controllerName);
            $popLast = array_pop($exploded_arr); //pick up last element from exploded array
            $controllerPrefix = str_replace('Controller', '', $popLast); //Remove Controller suffix
            $controller = strtolower($controllerPrefix);

            $acl = $this->resourceDump();
         
            if (!$acl->isAllowed($role, $moduleName, $controller . ':' . $restMethod)) {
                $res->setStatusCode(400); //Bad Request
                $this->setSuccess(false);
                $this->setMsg('You Are Not Authorized');
                $allowed = false;
            }
        } catch (Exception $exc) {
            $res->setStatusCode(417); //Expectation Failed
            $this->setData(OexceptionSniffer::exceptionScanner($exc));
            $allowed = false;
        }

        if (!$allowed) {
            $jsonModel = new JsonModel($this->getResult());
            $res->setContent($jsonModel->serialize());
        }

        return $res;
    }

}
