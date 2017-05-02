<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Oapiconfig\BaseProvider\OapisecurityServiceBaseProvider;
use Zend\View\Model\JsonModel;

/**
 * Description of OapiService
 *
 * @author OviMughal
 */
class OapisecurityService extends OapisecurityServiceBaseProvider
{

    public function apiKeyScanner()
    {
        $req = $this->getOserviceLocator()->get('Request');
        $res = $this->getOserviceLocator()->get('Response');
        $jsonModel = new JsonModel();
        $authHeader = $req->getHeader('X-Api-Key');

        $flag = true;
        if ($authHeader) {
            list($api_key) = sscanf($authHeader->toString(), 'X-Api-Key: %s');
            if ($api_key != $this->getApiKey()) {
                $flag = false;
                $res->setStatusCode(402); //Payment Required :)            
                $jsonModel->setVariables([
                    'success' => false,
                    'msg' => 'Inavlid Api Key',
                    'data' => [],
                ]);
            }
        } else {
            $flag = false;
            $res->setStatusCode(401); //Unauthorized
            $jsonModel->setVariables([
                'success' => false,
                'msg' => 'No Api Key',
                'data' => [],
            ]);
        }

        if (!$flag) {
            $res->setContent($jsonModel->serialize());
        }

        return $res;
    }

}
