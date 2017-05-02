<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Oapiconfig for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Oapiconfig;

use Zend\Mvc\MvcEvent;
class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        $config = [];

        $configFiles = [
            __DIR__ . '/../config/module.config.php',
            __DIR__ . '/../config/services.config.php',
            __DIR__ . '/../config/oconfig.config.php',
        ];

        // Merge all module config options
        foreach ($configFiles as $configFile) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
        }

        return $config;
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('route', array($this, 'loadConfiguration'), 1000);
         //$moduleRouteListener = new ModuleRouteListener();
         //$moduleRouteListener->attach($eventManager);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'authorizationScanner'));
       //$eventManager->attach(MvcEvent::EVENT_FINISH, array($this, 'thePileDriver'));
    }

    public function loadConfiguration(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();

        $router = $sm->get('router');
        $request = $sm->get('request');

        $matchedRoute = $router->match($request);
        if (null !== $matchedRoute) {
            $route = $matchedRoute->getMatchedRouteName(); //oapi-by Ovi
            if ('oapi' === substr($route, 0, 4)) {//oapi-by Ovi
                $sharedManager->attach('Zend\Mvc\Controller\AbstractRestfulController', 'dispatch', function($e) use ($sm) {
                    $sm->get('ControllerPluginManager')->get('GateKeeper')
                            ->routeIdentifier($e); //pass to the plugin...
                }, 1000
                );
            }
        } else {
            //oapi-by Ovi
            $path = ltrim($router->getRequestUri()->getPath(), '/');
            $pathFragments = explode('/', $path);

            if ('api' === $pathFragments[0]) {
                $res = $sm->get('response');
                $res->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $res->setContent(json_encode(['success' => false, 'msg' => 'Method Not Found', 'data' =>[]]));
                return $res->setStatusCode(404);
            }
        }
    }

    public function authorizationScanner($e)
    {
        if(405 == $e->getResponse()->getStatusCode()){
            $e->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $e->getResponse()->setContent(json_encode(['success' => false, 'msg' => 'Method Not Found', 'data' =>[]]));
            $e->stopPropagation();
            return $e->getResponse();
        }
        else if(404 == $e->getResponse()->getStatusCode()){
            $e->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $e->getResponse()->setContent(json_encode(['success' => false, 'msg' => 'Page Not Found', 'data' =>[]]));
            $e->stopPropagation();
            return $e->getResponse();
        }
        else if (200 != $e->getResponse()->getStatusCode()) {
            $e->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $e->stopPropagation();
            return $e->getResponse();
        }
    }

    /*
      public function thePileDriver($e)
      {
      $sm = $e->getApplication()->getServiceManager();
      $res = $sm->get('response');
      $status = $res->getStatusCode();

      $router = $sm->get('router');
      $path = ltrim($router->getRequestUri()->getPath(), '/');
      $pathFragments = explode('/', $path);

      if ('api' === $pathFragments[0]) {
      if (200 != $status) {
      // $res->getHeaders()->addHeaderLine('Content-Type', 'application/json');
      // echo $res->setContent($res->getContent().'');
      }
      }
      }
     */

//    public function thePileDriver($e)
//    {
//        $sm = $e->getApplication()->getServiceManager();
//        $oJwtizer = DI\ServiceInjector::oJwtizer();
//        $oJwt = $oJwtizer->getOjwt();
//        $oJwtExpire = $oJwtizer->getOjwtExpire();
//        
//        $res = $sm->get('response');
//
//        if (null != $oJwt) {
//            $res->getHeaders()->addHeaderLine('X_AUTH_TOKEN', json_encode([
//                'access_token' => $oJwt,
//                'token_type' => 'jwt',
//                'expires_in' => $oJwtExpire
//            ]));
//        }
//    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'doctObjMngr' => function($sm) {
                    $em = $sm->get('Doctrine\ORM\EntityManager');
                    return $em;
                }
            ),
        );
    }

}
