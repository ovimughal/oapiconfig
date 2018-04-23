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

    public static function iCurler()
    {
        /*
         * Methods that can be used from the caller
         * # getCurledImageData($imageName, $imageResource = null)
         * # getPictureData($rawData, $imageName)
         */
        return self::$serviceLocator->get('Oimagecurler');
    }

    /**
     * This gives you access to various methods to solve and handle
     * complex file operations and file structure.
     * 
     * Methods available are:
     * 
     * Get the root path of application from anywhere
     * 1. getAppRootPath() 
     * 
     * Get Folder path of given resource from anywhere. Resource name is the
     * Key given in config/autoload/oapiconfig.global.php
     * 2. getFolderPath($resourceName = null) 
     * 
     * Get value of Key anywhere given in config/autoload/oapiconfig.global.php
     * 3. getConfigValue($key)
     * 
     * Download file. Just pass filename & resource key in config/autoload/oapiconfig.global.php
     * 4. downloadFile($filename, $resourceName = null)
     * 
     * Get File as a data, & use it anywhere. You can also fetch file data from 
     * remote locations as well just fill `file_server` key in config/autoload/oapiconfig.global.php
     * 5. getFileData($fileName, $fileResource = null, $fromFileServer = false)
     * 
     * @author OviMughal
     */
    public static function oFileManager()
    {
        /*
         * Methods that can be used from the caller
         * # downloadFile($imageName, $folderName = null)
         */
        return self::$serviceLocator->get('Ofilemanager');
    }

}
