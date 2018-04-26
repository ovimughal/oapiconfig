<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Oapiconfig\BaseProvider\OapisecurityServiceBaseProvider;
use Oapiconfig\DI\ServiceInjector;
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
                $api_key = $this->keyDecoder($encodedKey);
                
                $this->apiKeyValidityScanner($api_key);
            } else {
                $this->noApiKey();
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
    
    public function keyDecoder($encodedKey)
    {
        $keySeperator = ServiceInjector::oFileManager()->getConfigValue('hyperlink_security_salt','api');
        $baseDecodedKey = base64_decode($encodedKey);
        list($salted_api_key,$jwt) = explode($keySeperator, $baseDecodedKey);
        
        $this->setAuthToken($jwt);
        
        $api_key = substr($salted_api_key, 5, -5);
        
        return $api_key;
    }
    
    public function setAuthToken($jwt){
        $this->req->getHeaders()->addHeaders(['authorization' => 'Bearer '.$jwt]);
    }

    public function noApiKey()
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
