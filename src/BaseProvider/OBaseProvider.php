<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

use Oapiconfig\DI\ServiceInjector;

/**
 * Description of OBaseProvider
 *
 * @author OviMughal
 */
class OBaseProvider
{

    private static $organizationId = null;

    public static function getOserviceLocator()
    {
        return ServiceInjector::$serviceLocator;
    }

    public function getOconfig()
    {
        return ServiceInjector::$serviceLocator->get('config');
    }

    public function getOconfigManager()
    {
        return $this->getOconfig()['oconfig_manager'];
    }

    public function appUrl()
    {
        return $this->getOconfigManager()['settings']['app_url'];
    }

    public function apiUrl()
    {
        return $this->getOconfigManager()['settings']['api_url'];
    }

    public function apiKey()
    {
        return $this->getOconfigManager()['api']['api_key'];
    }

    public function setOrganizationId($id)
    {
        self::$organizationId = $id;
    }

    public function organizationId(bool $asQueryString = false)
    {
        $req = self::getOserviceLocator()->get('Request');
        $tenantIdName = $this->getOconfigManager()['tenant']['tenant_id_name'];
        $organizationId = (int)$req->getQuery($tenantIdName);

        if (!$organizationId) {
            $organizationId = self::$organizationId??$organizationId;
        }

        if ($asQueryString) {
            $organizationId = $tenantIdName.'='.$organizationId;
        }
        return $organizationId;
    }

}
