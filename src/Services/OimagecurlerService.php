<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Oapiconfig\BaseProvider\OhandlerBaseProvider;

/**
 * Description of OimagecurlerService
 *
 * @author OviMughal
 */
class OimagecurlerService extends OhandlerBaseProvider
{

    public function getCurledImageData($imageName, $imageResource = null)
    {
        $response = new \Laminas\Http\Response\Stream();
        if (!empty($imageName) && null != $imageResource) {
            $imageServer = $this->getOconfigManager()['settings']['image_server'];
            $imagePath = $this->getOconfigManager()['settings'][$imageResource];
            $url = $imageServer . $imagePath . $imageName;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');

            $rawData = curl_exec($ch);

            //$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            curl_close($ch);

            if ('text/html;charset=UTF-8' !== $contentType) {
                $imgData = $this->getPictureData($rawData, $imageName);
            } else {
                $imgData = false;
            }
        } else {
            $response->setStatusCode(404);
        }

        if ($response->getStatusCode() != 200) {
            $this->setData(\Oapiconfig\Sniffers\OexceptionSniffer::exceptionScanner(new \Exception('Either filename is empty or not readable, Please verify filename')));
            $response = new \Laminas\View\Model\JsonModel($this->getResult());          
        } else {
            $response = $imgData;
        }
        
        return $response;
    }

    public function getPictureData($rawData, $imageName)
    {
        $imageData = base64_encode($rawData);
        $type = pathinfo($imageName, PATHINFO_EXTENSION);
        $imgData = 'data:image/' . $type . ';base64,' . $imageData;

        return $imgData;
    }

}
