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

// Optional.... for customization
//    public function getUserRoles() {
//        $dql = 'SELECT u.userrolename FROM '
//                . $this->getPath() . '\Userrole u WHERE u.hasdeleted=?1';
//        $params = [1 => 0];
//        $errMsg = 'User Roles Not Defined';
//        $result = $this->select($dql, $params, $errMsg);
//
//        return $result;
//    }
//    public function resourceDump() {
//        try {
//            $acl = new Acl();
//            $acl->deny();
//
//            $result = $this->getUserRoles();
//            if (!is_a($result, 'Exception')) {
//                foreach ($result as $r):
//                    $acl->addRole(new Role($r['userrolename']));
//                endforeach;
//                $acl->addRole(new Role('SuperAdmin'));
//
//                $acl->addResource('doctrineormmodule');
//                $acl->addResource('oapigps');
//                $acl->addResource('oapiemployeeprofile');
//                $acl->addResource('oapiemployeeattendance');
//                $acl->addResource('oapimastersettings');
//                $acl->addResource('oapisalesorder');
//
//                $acl->allow('Admin', 'oapiemployeeprofile', 'employeeprofile:GET');
//
//                $acl->allow('Admin', 'oapigps', 'gps:POST');
//                $acl->allow('Admin', 'oapigps', 'allemployeegps:GET');
//                $acl->allow('Admin', 'oapigps', 'singleemployeegps:GET');
//
//                $acl->allow('Admin', 'oapiemployeeattendance', 'employeeattendance:POST');
//
//                $acl->allow('Admin', 'oapimastersettings', 'mastersettings:GET');
//
//                //$acl->allow('Admin', 'oapisalesorder', 'salesorder:GET');
//            }
//            else {
//                throw new Exception($result->getMessage(), $result->getCode(), $result->getPrevious());
//            }
//        } catch (Exception $exc) {
//            throw new Exception($exc->getMessage(), $exc->getCode(), $exc->getPrevious());
//        }
//
//        return $acl;
//    }

    public function resourceDump()
    {
        $resources = $this->getOconfigManager()['resources'];
        $userRoles = $this->getOconfigManager()['userRoles'];
        $allowList = $this->getOconfigManager()['allowList'];
        try {
            $acl = new Acl();
            $acl->deny();
            if (null != $userRoles && null != $resources && null != $allowList) {
                $this->resourceLoader($acl, $userRoles, $resources, $allowList);
            } else {
                throw new Exception('Please specify Resources/User Role/Allow List');
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode(), $exc->getPrevious());
        }
        return $acl;
    }

    public function resourceLoader($acl, $userRoles, $resources, $allowList)
    {
        foreach ($userRoles as $role) {
            $acl->addRole(new Role($role));
        }
        foreach ($resources as $resource) {
            $acl->addResource($resource);
        }
        foreach ($allowList as $allowed) {
            //for multiple roles on same route
            if (is_array($allowed['role'])) {
                $this->roleCheck($acl, $allowed);
            } else {
                $this->routeCheck($acl, $allowed);
            }
        }
    }
    
    public function roleCheck($acl, $allowed)
    {
        //for multiple roles on same route
        foreach ($allowed['role'] as $role) {
            $allowed['role'] = $role;
            $this->routeCheck($acl, $allowed);
        }
    }

    public function routeCheck($acl, $allowed)
    {
        //mandatory route array check
        if (isset($allowed['route'])) {
            //for multiple routes in a module
            if (is_array($allowed['route'])) {
                foreach ($allowed['route'] as $route => $methods) {
                    $allowed['route'] = $route;
                    $this->methodCheck($acl, $allowed, $methods);
                }
            } else {
                throw new Exception("Route in Allow List should be of type Array");
            }
        }
        else{
            throw new Exception("Route not defined in Allow List");
        }
    }

    public function methodCheck(Acl $acl, $allowed, $methods)
    {
        //for multiple methods allowed on a route
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $acl->allow($allowed['role'], $allowed['module'], $allowed['controller'] . ':' . $allowed['route']. ':' . $method);
            }
        } else {
            $method = $methods;
            $acl->allow($allowed['role'], $allowed['module'], $allowed['controller'] . ':' . $allowed['route']. ':' . $method);
        }
    }

    public function authorizationCheck($e)
    {
        $res = $e->getResponse();
        $allowed = true;
        try {
            $role = $this->getRole();
            $acl = $this->resourceDump();
            $result = $this->requestAnalyzer($e);
            if (!$acl->isAllowed($role, $result['module'], $result['controller'] . ':' . $result['route'].  ':' . $result['method'])) {
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

    public function requestAnalyzer($e)
    {
        $controllerTarget = $e->getTarget();
        $controllerClass = get_class($controllerTarget);
        $moduleName = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
        $routeMatch = $e->getRouteMatch();
        //start new for oRest
        $fullRoute = $routeMatch->getMatchedRouteName();
        $fullRouteArr = explode('/', $fullRoute);
        $route = $fullRouteArr[0];
        //end new for oRest
        $restMethod = $e->getRequest()->getMethod();
        $controllerName = $routeMatch->getParam('controller', 'not-found');
        $exploded_arr = explode('\\', $controllerName);
        $popLast = array_pop($exploded_arr); //pick up last element from exploded array
        $controllerPrefix = str_replace('Controller', '', $popLast); //Remove Controller suffix
        $controller = strtolower($controllerPrefix);

        return [
            'module' => $moduleName,
            'controller' => $controller,
            'route' => $route, //new for oRest
            'method' => $restMethod
        ];
    }

}
