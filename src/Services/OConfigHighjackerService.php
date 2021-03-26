<?php

namespace Oapiconfig\Services;

use Exception;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;
use Oapiconfig\DI\ServiceInjector;

class OConfigHighjackerService
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function getServiceLocator(): ContainerInterface
    {
        return $this->container;
    }

    private function overrideConfig(array $config)
    {
        /**
         * @var ServiceManager
         */
        $sm = $this->getServiceLocator();
        $sm->setAllowOverride(true);
        $sm->setService('config', $config);
        // $sm->setService('doctObjMngr', function($sm) {
        //         $em = $sm->get('doctrine.entitymanager.orm_tenant');
        //         return $em;
        //     }
        // );
        $sm->setAllowOverride(false);
    }

    public function overrideDbConfig(?array $tenantData = null)
    {
        try {
            $tenantData = $tenantData ?? ServiceInjector::oJwtizer()->getTenantInfo(); // not using any info within JWT
            $oconfig = $this->getServiceLocator()->get('config');
            if($oconfig['oconfig_manager']['settings']['enable_multitenancy']){
                if (null === $tenantData) {
                    throw new Exception('Wrong tenant data');
                }
            }
            if (null !== $tenantData) {
                $host = $tenantData['host'];
                $port = $tenantData['port'];
                $username = $tenantData['username'];
                $password = ServiceInjector::oEncryption()->keyDecoder($tenantData['password']);
                $database = $tenantData['database'];
                
                $oconfig['doctrine']['connection']['orm_tenant']['params'] = [
                    'host'     => $host,
                    'user'     => $username,
                    'password' => $password,
                    'dbname'   => $database,
                    'port' => $port,
                    'driverOptions' => array(
                        'CharacterSet' => 'UTF-8'
                    )
                ];

                $oconfig['oconfig_manager']['settings']['dbms_server'] = $host;
                $oconfig['oconfig_manager']['settings']['data_base_name'] = $database;
                $oconfig['oconfig_manager']['settings']['data_base_user'] = $username;
                $oconfig['oconfig_manager']['settings']['data_base_password'] = $password;

                $this->overrideConfig($oconfig);
            }
        } catch (Exception $exc) {
            throw new Exception($exc);
        }
    }
}
