<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Sniffers;

use Oapiconfig\BaseProvider\OhandlerBaseProvider;

/**
 * Description of OexceptionSniffer
 *
 * @author OviMughal
 */
class OexceptionSniffer extends OhandlerBaseProvider
{

    public static function exceptionScanner($result)
    {
        if (is_a($result, 'Exception')) {
            $res = parent::getOserviceLocator()->get('Response');
            $res->setStatusCode(417); //Expectation Failed
            
            if (ENV) {
                $result = $result->getMessage();
            } else {
                $result = 'Exc-Please Contact Administrator';
            }
            parent::setSuccess(false);
            parent::setMsg('An Exception Occured');
        }

        if (!count($result)) {
            $result = (object) null;
        }

        return $result;
    }

}
