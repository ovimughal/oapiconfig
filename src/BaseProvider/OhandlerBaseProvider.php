<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\BaseProvider;

/**
 * Description of OhandlerBaseProvider
 *
 * @author OviMughal
 */
class OhandlerBaseProvider extends OBaseProvider
{

    private static $success;
    private static $notificationType;
    private static $notificationDisplay;
    private static $msg;
    private static $data;

    public function __construct()
    {
        self::initHandler();
    }

    private static function initHandler()
    {
        self::setSuccess(true);
        self::setNotificationType('success');
        self::setNotificationDisplay(true);
        self::setMsg('Executed Successfully');
        self::setData((object) null);
    }

    public static function resetHandler()
    {
        self::initHandler();
    }

    public static function setSuccess($success)
    {
        self::$success = $success;
    }

    public static function getSuccess()
    {
        return self::$success;
    }

    public static function setNotificationType($notificationType)
    {
        self::$notificationType = $notificationType;
    }

    public static function getNotificationType()
    {
        return self::$notificationType;
    }

    public static function setNotificationDisplay($notificationDisplay)
    {
        self::$notificationDisplay = $notificationDisplay;
    }

    public static function getNotificationDisplay()
    {
        return self::$notificationDisplay;
    }

    public static function setMsg($msg)
    {
        self::$msg = self::msgScanner($msg);
    }

    public static function getMsg()
    {
        return self::$msg;
    }

    public static function setData($data)
    {
        self::$data = $data;
    }

    public static function getData()
    {
        return self::$data;
    }

    public static function getResult()
    {
        $result = [
            'success' => self::getSuccess(),
            'notificationType' => self::getNotificationType(),
            'notificationDisplay' => self::getNotificationDisplay(),
            'msg' => self::getMsg(),
            'data' => self::getData()
        ];

        self::resetHandler();
        return $result;
    }

    private static function defaultLanguage()
    {
        $defaultLanguage = 'en';
        $class_name = '\GlobalProcedure\Model\GlobalProcedureModel';
        if (class_exists($class_name)) {
            $globalProcedure = new \GlobalProcedure\Model\GlobalProcedureModel();
            $userPrefrences = $globalProcedure->getUserPrefrences();
            $defaultLanguage = $userPrefrences['language'];
        }
        
        return $defaultLanguage;
    }

    public static function languageScanner()
    {
        $language = \Oapiconfig\DI\ServiceInjector::oLanguage()->getLanguage();

//        if (null === $language) {
//            $language = self::defaultLanguage();
//        }

        return $language;
    }

    private static function msgScanner($msgs)
    {
        $language = self::languageScanner();
        
        $msg = '';
        if (is_array($msgs)) {
            if (array_key_exists($language, $msgs)) {
                $msg = $msgs[$language];
            } else {
                $msg = $msgs[0];
            }
        } else {
            $msg = $msgs;
        }

        return $msg;
    }

}
