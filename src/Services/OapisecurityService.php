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

    private $req;
    private $res;
    private $jsonModel;
    private $flag;

    public function apiKeyScanner()
    {
        $this->req = $this->getOserviceLocator()->get('Request');
        $this->res = $this->getOserviceLocator()->get('Response');
        $this->jsonModel = new JsonModel();

        $authHeader = $this->req->getHeader('X-Api-Key');

        $this->flag = true;
        if ($authHeader) {
            list($api_key) = sscanf($authHeader->toString(), 'X-Api-Key: %s');
            $this->apiKeyValidityScanner($api_key);
        } else {
            if ($this->req->getQuery('key')) {
                $encodedKey = $this->req->getQuery('key');
                $key = substr($encodedKey, 5, -5);
                $this->apiKeyValidityScanner($key);
            } else {
                $this->unauthorizedApiKey();
            }
        }

        if (!$this->flag) {
            $this->res->setContent($this->jsonModel->serialize());
        }
        return $this->res;
    }

    public function apiKeyValidityScanner($api_key)
    {
        if ($api_key != $this->getApiKey()) {
            $this->flag = false;
            $this->res->setStatusCode(402); //Payment Required :)
            $this->jsonModel->setVariables([
                'success' => false,
                'msg' => 'Inavlid Api Key',
                'data' => (object) null,
            ]);
        }
    }

    public function unauthorizedApiKey()
    {
        $this->flag = false;
        $this->res->setStatusCode(401); //Unauthorized
        $this->jsonModel->setVariables([
            'success' => false,
            'msg' => 'No Api Key',
            'data' => (object) null,
        ]);
    }

}
