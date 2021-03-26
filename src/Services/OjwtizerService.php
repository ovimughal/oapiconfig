<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Oapiconfig\BaseProvider\OjwtizerServiceBaseProvider;
use Oapiconfig\DI\ServiceInjector;
use Laminas\View\Model\JsonModel;

/**
 * Description of OjwtizerService
 *
 * @author OviMughal
 */
class OjwtizerService extends OjwtizerServiceBaseProvider
{

    public function ojwtGenerator(): string
    {
        $jwt = JWT::encode(
            $this->getPayload(),
            $this->getSaltedKey(),
            $this->getAlgo()
        );
        return $jwt;
    }

    public function oJwtify(array $userData, array $tenantData = null): void
    {
        /**
         * @var Response
         */
        $res = $this->getSl()->get('Response');
        $this->setPayload($userData, $tenantData);
        $this->setUserInfo($userData); // so that as soon as user logs in, data becomes available
        $this->setTenantInfo($tenantData);
        $this->setOjwt($this->ojwtGenerator());
        $oJwt = $this->getOjwt();
        $oJwtExpiresIn = $this->getOjwtExpiresIn();
        if (null != $oJwt) {
            $res->getHeaders()->addHeaderLine('X-Auth-Token', json_encode([
                'access_token' => $oJwt,
                'token_type' => 'jwt',
                'expires_in' => $oJwtExpiresIn
            ]));
        }
    }

    public function ojwtValidator() : Response
    {
        /**
         * @var Response
         */
        $res = $this->getSl()->get('Response');
        /**
         * @var Request
         */
        $req = $this->getSl()->get('Request');
        $authHeader = $req->getHeader('authorization');

        if ($authHeader) {
            list($jwt) = sscanf($authHeader->toString(), 'Authorization: Bearer %s');

            if ($jwt) {
                $this->setOjwt($jwt); // sets user sent jwt everytime. Done only for hyperlinkEncodedKey() method
                try {
                    // Adjust colck skew since angular app & apis are at different locations
                    JWT::$leeway = 10;
                    $token = JWT::decode($jwt, $this->getSaltedKey(), array($this->getAlgo()));
                    $this->setUserInfo((array) $token->userData);
                    $this->setTenantInfo((array) $token->tenantData);
                } catch (ExpiredException $exExc) {
                    $res->setStatusCode(401); //unauthorized basically it means user is unauthenticated
                    $this->setSuccess(false);
                    $this->setMsg('Token Expired');
                } catch (\Exception $exc) {
                    $res->setStatusCode(401); //unauthorized basically it means user is unauthenticated
                    $this->setSuccess(false);
                    $this->setMsg('Invalid Token');
                }
            } else {
                $res->setStatusCode(401); //unauthorized basically it means user is unauthenticated
                $this->setSuccess(false);
                $this->setMsg('No Bearer');
            }
        } else {
            $res->setStatusCode(401); //unauthorized basically it means user is unauthenticated
            $this->setSuccess(false);
            $this->setMsg('No Authorization Token');
        }

        if (!isset($token)) {
            $jsonModel = new JsonModel($this->getResult());
            $res->setContent($jsonModel->serialize());
        }
        return $res;
    }

    public function getSaltedKey() : string
    {
        return $this->getKey() . $this->getTokenSalt() . $this->getServer();
    }

    public function getTokenSalt() : string
    {
        $config = parse_ini_file(__DIR__ . '/token.ini');
        $tokenSalt = $config['token_salt'];

        return $tokenSalt;
    }

    public function invalidateJWT() : void
    {
        try {

            $config = parse_ini_file(__DIR__ . '/token.ini');
            $config['token_salt'] = time();

            $f = fopen(__DIR__ . '/token.ini', 'w');
            foreach ($config as $name => $value) {
                fwrite($f, "$name = $value\n");
            }
            fclose($f);
        } catch (Exception $exc) {
            throw new Exception($exc);
        }
    }
}
