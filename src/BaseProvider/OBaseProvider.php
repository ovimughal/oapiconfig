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

}
