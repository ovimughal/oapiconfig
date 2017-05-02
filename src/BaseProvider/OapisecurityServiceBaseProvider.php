<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

/**
 * Description of OapiServiceBaseProvider
 *
 * @author OviMughal
 */
class OapisecurityServiceBaseProvider extends OBaseProvider
{
    private $apiKey;
    
    public function __construct()
    {
        $apiKey = $this->getOconfigManager()['api']['api_key'];
        $this->setApiKey($apiKey);
    }
    
    public function setApiKey($apiKey){
    $this->apiKey = $apiKey;
    }
    
    public function getApiKey(){
        return isset($this->apiKey) ? $this->apiKey : 'My_Secret_API_Key';
    }
}
