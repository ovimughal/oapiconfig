<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\DI;

/**
 * Description of ServiceInjector
 *
 * @author OviMughal
 */
class ServiceInjector
{

    public static $serviceLocator;

    public static function oJwtizer()
    {
        /*
         * Methods that can be used from the caller
         * # oJwtify($userData)
         */
        return self::$serviceLocator->get('Ojwtizer');
    }

    public static function oOrm()
    {
        /*
         * Methods that can be used from the caller
         * # getDoctObjMngr()
         * # entityHydrator()
         * # getServiceLocator()
         * # getEntityPath()
         */
        return self::$serviceLocator->get('Oorm');
    }
    
    public static function iCurler(){
        /*
         * Methods that can be used from the caller
         * # getCurledImageData($imageName, $imageResource = null)
         * # getPictureData($rawData, $imageName)
         */
        return self::$serviceLocator->get('Oimagecurler');
    }
    
    public static function oFileManager(){
        /*
         * Methods that can be used from the caller
         * # downloadFile($imageName, $folderName = null)
         */
        return self::$serviceLocator->get('Ofilemanager');
    }

}
