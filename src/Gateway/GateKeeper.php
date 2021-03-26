<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Gateway;

use Laminas\Http\Response;
use Oapiconfig\DI\ServiceInjector;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Oapiconfig\Services\OaclService;
use Oapiconfig\Services\OapisecurityService;
use Oapiconfig\Services\OConfigHighjackerService;
use Oapiconfig\Services\OjwtizerService;
use Oapiconfig\Services\OlanguageService;

/**
 * Description of GateKeeper
 *
 * @author OviMughal
 */
class GateKeeper extends AbstractPlugin
{

    public function routeIdentifier(\Laminas\Mvc\MvcEvent $e): Response
    {
        $this->injectServiceLocator($e);
        //        $this->appLanguage($e);
        /**
         * @var Response
         */
        $res = $this->isApiKeyValid($e);

        $oConfigMngr = ServiceInjector::$serviceLocator->get('config')['oconfig_manager'];
        $loginEnabled = $oConfigMngr['settings']['enable_login'];
        $appDevEnv = $oConfigMngr['settings']['app_development_env'];
        $openIdentityRoutes = $oConfigMngr['open_identity_routes'];
        $openAccessRoutes = $oConfigMngr['open_access_routes'];
        define('ENV', is_bool($appDevEnv) ? $appDevEnv : true);

        if ($res->getStatusCode() == 200) {
            $fullRoute = $e->getRouteMatch()->getMatchedRouteName();
            $routeArr = explode('/', $fullRoute);
            $route = ($routeArr && count($routeArr)) ? $routeArr[0] : null;
            if($route && !in_array($route, $openIdentityRoutes)){
            // if ('login' != $routeArr[0]) {
                if ($loginEnabled) {
                    $res = $this->identify();
                }
                if ($res->getStatusCode() == 200) {
                    $res = $this->tenantScanner();
                    if ($res->getStatusCode() == 200 && !in_array($route, $openAccessRoutes)) {
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

    private function identify(): Response
    {
        $ojwtManager = ServiceInjector::oJwtizer();
        return $ojwtManager->ojwtValidator();
    }

    private function tenantScanner() : Response
    {
        $tenant = ServiceInjector::oTenant();
        return $tenant->tenantIdentifier();
        // $configHighjacker = ServiceInjector::oConfigHighjacker();
        // // $configHighjacker = new OConfigHighjacker(ServiceInjector::$serviceLocator);
        // return $configHighjacker->overrideDbConfig();
    }

    private function injectServiceLocator(\Laminas\Mvc\MvcEvent $e): void
    {
        ServiceInjector::$serviceLocator = $e->getApplication()->getServiceManager();
    }

    private function isApiKeyValid(\Laminas\Mvc\MvcEvent $e): Response
    {
        $sm = $e->getApplication()->getServiceManager();
        /**
         * @var OapisecurityService
         */
        $oapiSecurityManager = $sm->get('Oapisecurity');
        return $oapiSecurityManager->apiKeyScanner();
    }

    private function accessVerifier(\Laminas\Mvc\MvcEvent $e): Response
    {
        $sm = $e->getApplication()->getServiceManager();
        /**
         * @var OaclService
         */
        $oaclManager = $sm->get('Oacl');
        return $oaclManager->authorizationCheck($e);
    }

    // public function appLanguage(\Laminas\Mvc\MvcEvent $e): void
    // {
    //     $sm = $e->getApplication()->getServiceManager();
    //     /**
    //      * @var OlanguageService
    //      */
    //     $oLanguage = $sm->get('Olanguage');
    //     $oLanguage::extractLanguage($sm);
    // }
}
