<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Oapiconfig\BaseProvider\OjwtizerServiceBaseProvider;
use Zend\View\Model\JsonModel;

/**
 * Description of OjwtizerService
 *
 * @author OviMughal
 */
class OjwtizerService extends OjwtizerServiceBaseProvider
{

    public function ojwtGenerator()
    {
        $jwt = JWT::encode(
                        $this->getPayload(), $this->getKey(), $this->getAlgo()
        );
        return $jwt;
    }

    public function oJwtify($userData)
    {
        $res = $this->getSl()->get('Response');
        $this->setPayload($userData);
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

    public function ojwtValidator()
    {
        $res = $this->getSl()->get('Response');
        $req = $this->getSl()->get('Request');
        $authHeader = $req->getHeader('authorization');

        if ($authHeader) {
            list($jwt) = sscanf($authHeader->toString(), 'Authorization: Bearer %s');

            if ($jwt) {
                try {
                    //JWT::$leeway = 60;
                    $token = JWT::decode($jwt, $this->getKey(), array($this->getAlgo()));
                    $this->setUserInfo((array) $token->data);
                } catch (ExpiredException $exExc) {
                    $res->setStatusCode(401); //unauthorized
                    $this->setSuccess(false);
                    $this->setMsg('Token Expired');
                } catch (\Exception $exc) {
                    $res->setStatusCode(401); //unauthorized
                    $this->setSuccess(false);
                    $this->setMsg('Invalid Token');
                }
            } else {
                $res->setStatusCode(401); //unauthorized
                $this->setSuccess(false);
                $this->setMsg('No Bearer');
            }
        } else {
            $res->setStatusCode(401); //unauthorized
            $this->setSuccess(false);
            $this->setMsg('No Authorization Token');
        }

        if (!isset($token)) {
            $jsonModel = new JsonModel($this->getResult());
            $res->setContent($jsonModel->serialize());
        }
        return $res;
    }

}
