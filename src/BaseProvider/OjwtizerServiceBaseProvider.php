<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

/**
 * Description of OjwtizerDataService
 *
 * @author OviMughal
 */
class OjwtizerServiceBaseProvider extends OhandlerBaseProvider
{
    private $sl;
    private $key;
    private $algo;
    private $data;
    private $server;
    private $iatOffset;
    private $expOffset;
    private $userInfo;
    private $oJwt;
    private $oJwtExpiresIn;

    public function __construct($sl, $config)
    {
        parent::__construct();
        
        $this->setSl($sl);
        $this->setKey($config['jwt_key']);
        $this->setAlgo($config['algo']);
        $this->setServer($config['server']);
        $this->setIatOffset($config['iatOffset']);
        $this->setExpOffset($config['expOffset']);
    }

    public function payloadInit()
    {

        $payloadConfig['tokenId'] = base64_encode(mcrypt_create_iv(32));
        $payloadConfig['issuedAt'] = time();
        $payloadConfig['notBefore'] = $payloadConfig['issuedAt'] + $this->getIatOffset();             //Adding 10 seconds
        $payloadConfig['expire'] = $payloadConfig['notBefore'] + $this->getExpOffset();            // Adding 60 seconds

        return $payloadConfig;
    }

    public function setPayload($userData)
    {
        $payloadConfig = $this->payloadInit();

        $tokenId = $payloadConfig['tokenId'];
        $issuedAt = $payloadConfig['issuedAt'];
        $notBefore = $payloadConfig['notBefore']; 
        $expire = $payloadConfig['expire'];  
        
        $this->setOjwtExpiresIn($expire);

        $this->data = [
            'iat' => $issuedAt,
            'jti' => $tokenId,
            'iss' => $this->getServer(), // Issuer
            'nbf' => $notBefore, // Not before
            'exp' => $expire, // Expire
            'data' => $userData
        ];
    }
    
     public function getPayload()
    {
        return $this->data;
    }
    
    public function setSl($sl)
    {
        $this->sl = $sl;
    }
    
    public function getSl()
    {
        return isset($this->sl) ? $this->sl : null;
    }

    public function setUserInfo($userInfo)
    {
        $this->userInfo = $userInfo;
    }

    public function getUserInfo()
    {
        return isset($this->userInfo) ? $this->userInfo : null;
    }

    public function setOjwt($oJwt)
    {
        $this->oJwt = $oJwt;
    }

    public function getOjwt()
    {
        return isset($this->oJwt) ? $this->oJwt : null;
    }

    public function setOjwtExpiresIn($oJwtExpiresIn)
    {
        $this->oJwtExpiresIn = $oJwtExpiresIn;
    }
    
    public function getOjwtExpiresIn()
    {
        return isset($this->oJwtExpiresIn) ? $this->oJwtExpiresIn - time() : null;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }
    
    public function getKey()
    {
        return isset($this->key) ? $this->key : 'My_Secret_Key';
    }

    public function setAlgo($algo)
    {
        $this->algo = $algo;
    }
    
    public function getAlgo()
    {
        return isset($this->algo) ? $this->algo : 'HS512';
    }
    
    public function setIatOffset($iatOffset)
    {
        $this->iatOffset = $iatOffset;
    }
    
    public function getIatOffset()
    {
        return isset($this->iatOffset) ? $this->iatOffset : 10; //10 sec
    }
    
    public function setExpOffset($expOffset)
    {
        $this->expOffset = $expOffset;
    }
    
    public function getExpOffset()
    {
        return isset($this->expOffset) ? $this->expOffset : 86400; // 24hrs
    }
    
    public function setServer($server)
    {
        $this->server = $server;
    }
    
    public function getServer()
    {
        return isset($this->server) ? $this->server : 'http://127.0.0.1:80'; //localhost
    }
}
