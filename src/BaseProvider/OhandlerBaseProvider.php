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
    private static $msg;
    private static $data;

    public function __construct()
    {
        self::initHandler();
    }

    private static function initHandler()
    {
        self::setSuccess(true);
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

    public static function setMsg($msg)
    {
        self::$msg = $msg;
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
            'msg' => self::getMsg(),
            'data' => self::getData()
        ];

        self::resetHandler();
        return $result;
    }

}
