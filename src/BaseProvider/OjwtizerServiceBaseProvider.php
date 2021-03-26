<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

use Interop\Container\ContainerInterface;

/**
 * Description of OjwtizerDataService
 *
 * @author OviMughal
 */
class OjwtizerServiceBaseProvider extends OhandlerBaseProvider
{
    private ?ContainerInterface $sl;
    private string $key;
    private string $algo;
    private ?array $data;
    private string $server;
    private int $iatOffset;
    private int $expOffset;
    private ?array $userInfo;
    private ?string $oJwt;
    private ?int $oJwtExpiresIn;
    private ?array $tenantInfo;

    public function __construct(ContainerInterface $sl, array $config)
    {
        parent::__construct();
        
        $this->setSl($sl);
        $this->setKey($config['jwt_key']);
        $this->setAlgo($config['algo']);
        $this->setServer($config['server']);
        $this->setIatOffset($config['iatOffset']);
        $this->setExpOffset($config['expOffset']);
    }

    /**
     * Initialises and return jwt payload
     *
     * @return array {tokenId: string, issuedAt: int, notBefore: int, expire: int}
     */
    public function payloadInit() : array
    {

        $payloadConfig['tokenId'] = base64_encode(random_bytes(32));
        $payloadConfig['issuedAt'] = time();
        $payloadConfig['notBefore'] = $payloadConfig['issuedAt'] + $this->getIatOffset();             //Adding 10 seconds
        $payloadConfig['expire'] = $payloadConfig['notBefore'] + $this->getExpOffset();            // Adding 60 seconds

        return $payloadConfig;
    }

    public function setPayload(array $userData, array $tenantData = null) : void
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
            'userData' => $userData,
            'tenantData' => $tenantData
        ];
    }
    
     public function getPayload() : array
    {
        return $this->data;
    }
    
    public function setSl(ContainerInterface $sl)
    {
        $this->sl = $sl;
    }
    
    public function getSl() : ?ContainerInterface
    {
        return isset($this->sl) ? $this->sl : null;
    }

    public function setUserInfo(array $userInfo) : void
    {
        $this->userInfo = $userInfo;
    }

    public function getUserInfo() : ?array
    {
        return isset($this->userInfo) ? $this->userInfo : null;
    }

    public function setOjwt(string $oJwt)
    {
        $this->oJwt = $oJwt;
    }

    public function getOjwt() : ?string
    {
        return isset($this->oJwt) ? $this->oJwt : null;
    }

    public function setOjwtExpiresIn(int $oJwtExpiresIn) : void
    {
        $this->oJwtExpiresIn = $oJwtExpiresIn;
    }
    
    public function getOjwtExpiresIn() : ?int
    {
        return isset($this->oJwtExpiresIn) ? $this->oJwtExpiresIn - time() : null;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
    }
    
    public function getKey() : string
    {
        return isset($this->key) ? $this->key : 'My_Secret_Key';
    }

    public function setAlgo(string $algo)
    {
        $this->algo = $algo;
    }
    
    public function getAlgo() : string
    {
        return isset($this->algo) ? $this->algo : 'HS512';
    }
    
    public function setIatOffset(int $iatOffset)
    {
        $this->iatOffset = $iatOffset;
    }
    
    public function getIatOffset() : int
    {
        return isset($this->iatOffset) ? $this->iatOffset : 10; //10 sec
    }
    
    public function setExpOffset(int $expOffset)
    {
        $this->expOffset = $expOffset;
    }
    
    public function getExpOffset() : int
    {
        return isset($this->expOffset) ? $this->expOffset : 86400; // 24hrs
    }
    
    public function setServer(string $server)
    {
        $this->server = $server;
    }
    
    public function getServer() : string
    {
        return isset($this->server) ? $this->server : 'http://127.0.0.1:80'; //localhost
    }

    public function setTenantInfo(?array $tenantInfo) : void
    {
        $this->tenantInfo = $tenantInfo;
    }

    public function getTenantInfo() : ?array
    {
        return isset($this->tenantInfo) ? $this->tenantInfo : null;
    }
}
