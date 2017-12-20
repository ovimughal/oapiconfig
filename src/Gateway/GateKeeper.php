<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Gateway;

use Oapiconfig\DI\ServiceInjector;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Description of GateKeeper
 *
 * @author OviMughal
 */
class GateKeeper extends AbstractPlugin {

    public function routeIdentifier($e) {
        define('ENV', getenv('APPLICATION_ENV') == 'development' ? true : false);

        $this->injectServiceLocator($e);
        $res = $this->isApiKeyValid($e);
        if ($res->getStatusCode() == 200) {
            $route = $e->getRouteMatch()->getMatchedRouteName();
            if ('login' != $route ||
                    (('login' == $route) &&
                    ('POST' != $e->getRequest()->getMethod()))
            ) {
                $res = $this->identify($e);
                if ($res->getStatusCode() == 200) {
                    $res = $this->accessVerifier($e);
                }
            }
        }

        return $res;
    }

    public function identify($e) {
        $sm = $e->getApplication()->getServiceManager();
        $ojwtManager = $sm->get('Ojwtizer');
        return $ojwtManager->ojwtValidator();
    }

    public function injectServiceLocator($e) {
        ServiceInjector::$serviceLocator = $e->getApplication()->getServiceManager();
    }

    public function isApiKeyValid($e) {
        $sm = $e->getApplication()->getServiceManager();
        $oapiSecurityManager = $sm->get('Oapisecurity');
        return $oapiSecurityManager->apiKeyScanner();
    }

    public function accessVerifier($e) {
        $sm = $e->getApplication()->getServiceManager();
        $oaclManager = $sm->get('Oacl');
        return $oaclManager->authorizationCheck($e);
    }

}
