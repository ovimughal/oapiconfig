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

    public static function oJwtizer(): \Oapiconfig\Services\OjwtizerService
    {
        /*
         * Methods that can be used from the caller
         * # oJwtify($userData)
         */
        return self::$serviceLocator->get('Ojwtizer');
    }

    public static function oOrm(): \Oapiconfig\Services\OormService
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

    public static function iCurler(): \Oapiconfig\Services\OimagecurlerService
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
    public static function oFileManager(): \Oapiconfig\Services\OfilemanagerService
    {
        /*
         * Methods that can be used from the caller
         * # downloadFile($imageName, $folderName = null)
         */
        return self::$serviceLocator->get('Ofilemanager');
    }

    public static function oLanguage(): \Oapiconfig\Services\OlanguageService
    {
        /*
         * Methods that can be used from the caller
         * # setLanguage($language)
         * # getLanguage()
         */
        return self::$serviceLocator->get('Olanguage');
    }

}
