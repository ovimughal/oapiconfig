<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Sniffers;

use Oapiconfig\BaseProvider\OhandlerBaseProvider;

/**
 * Description of OvalidationService
 *
 * @author OviMughal
 */
class OvalidationSniffer extends OhandlerBaseProvider
{

    public static function isEmpty($dataArr)
    {
        $res = false;
        foreach ($dataArr as $data) {
            if ('' == $data) {
                $res = true;
                parent::setSuccess(false);
                parent::setMsg('Required Fields Are Empty');
                break;
            }
        }

        return $res;
    }

    public static function isEmail($dataArr)
    {
        
    }

    public static function isInt($val)
    {
        $flag = true;

        if (!is_int($val)) {
            $flag = false;
            parent::setSuccess(false);
            parent::setMsg('Invalid Integer Supplied');
        }
        
        return $flag;
    }

    public static function isNumeric($val)
    {
        $flag = true;

        if (!is_numeric($val)) {
            $flag = false;
            parent::setSuccess(false);
            parent::setMsg('Invalid Number Supplied');
        }
        
        return $flag;
    }

    public static function isFloat($dataArr)
    {
        
    }

    public static function hasSpace($dataArr)
    {
        
    }

    public static function haveRequiredArgs($inputDataArr, $lookUpDataArr)
    {
        $flag = true;
        foreach ($lookUpDataArr as $lookUp) {
            if (!array_key_exists($lookUp, $inputDataArr)) {
                $flag = false;
                parent::setSuccess(false);
                parent::setMsg('Required Fields Not Supplied');
                break;
            }
        }

        return $flag;
    }

    public static function isDate($date)
    {
        $flag = true;
        if (!strtotime($date)) {
            $flag = false;
            parent::setSuccess(false);
            parent::setMsg('Not A Valid Date Supplied');
        }

        return $flag;
    }

}
