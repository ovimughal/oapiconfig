<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Gateway;

use Oapiconfig\DI\ServiceInjector;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Description of GateKeeper
 *
 * @author OviMughal
 */
class GateKeeper extends AbstractPlugin
{

    public function routeIdentifier(\Laminas\Mvc\MvcEvent $e)
    {
        $this->injectServiceLocator($e);
//        $this->appLanguage($e);
        $res = $this->isApiKeyValid($e);

        $oConfigMngr = ServiceInjector::$serviceLocator->get('config')['oconfig_manager'];
        $loginEnabled = $oConfigMngr['settings']['enable_login'];
        $appDevEnv = $oConfigMngr['settings']['app_development_env'];
        define('ENV', is_bool($appDevEnv) ? $appDevEnv : true);

        if ($res->getStatusCode() == 200) {
            $fullRoute = $e->getRouteMatch()->getMatchedRouteName();
            $routeArr = explode('/', $fullRoute);
            if ('login' != $routeArr[0]) {
                if ($loginEnabled) {
                    $res = $this->identify($e);
                }
                if ($res->getStatusCode() == 200) {
                    if ('token_reissue' != $routeArr[0]) {
                        $res = $this->accessVerifier($e);
                    }
                }
            }
            // $route = 'login/post' == $e->getRouteMatch()->getMatchedRouteName() ? 'login' : $e->getRouteMatch()->getMatchedRouteName();
//            if ('login' != $route ||
//                    (('login' == $route) &&
//                    ('POST' != $e->getRequest()->getMethod()))
//            ) {
//                if($loginEnabled){
//                $res = $this->identify($e);
//                }
//                if ($res->getStatusCode() == 200) {
//                    $res = $this->accessVerifier($e);
//                }
//            }
        }

        return $res;
    }

    public function identify($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $ojwtManager = $sm->get('Ojwtizer');
        return $ojwtManager->ojwtValidator();
    }

    public function injectServiceLocator($e)
    {
        ServiceInjector::$serviceLocator = $e->getApplication()->getServiceManager();
    }

    public function isApiKeyValid($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $oapiSecurityManager = $sm->get('Oapisecurity');
        return $oapiSecurityManager->apiKeyScanner();
    }

    public function accessVerifier($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $oaclManager = $sm->get('Oacl');
        return $oaclManager->authorizationCheck($e);
    }
    
    public function appLanguage($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $sm->get('Olanguage')::extractLanguage($sm);
    }

}
