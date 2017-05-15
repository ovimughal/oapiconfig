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

    public static function isEmpty($data)
    {
        $res = false;
        if (is_array($data)) {
            foreach ($data as $val) {
                if ('' == $val) {
                    $res = true;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($data) . ' Cannot Be Empty But Required Fields Are Empty');
                    break;
                }
            }
        } else {
            if ('' == $data) {
                $res = true;
                parent::setSuccess(false);
                parent::setMsg(json_encode($data) . ' Cannot Be Empty But Required Fields Are Empty');
            }
        }

        return $res;
    }

    public static function isEmail($dataArr)
    {

    }

    public static function isInt($dataList, $ignoreListRaw = null)
    {
        $flag = true;

        if (is_array($dataList)) {

            $ignoreList = array_flip($ignoreListRaw);
            $data = array_diff_key($dataList, $ignoreList);

            foreach ($data as $val) {
                if (!is_int($val)) {
                    $flag = false;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($data) . ' Need To be Integer But Invalid Integer Supplied');
                    break;
                }
            }
        } else {
            if (!is_int($dataList)) {
                $flag = false;
                parent::setSuccess(false);
                parent::setMsg(json_encode($dataList) . ' Need To be Integer But Invalid Integer Supplied');
            }
        }

        return $flag;
    }

    public static function isNumeric($dataList, $ignoreListRaw = null)
    {
        $flag = true;

        if (is_array($dataList)) {

            $ignoreList = array_flip($ignoreListRaw);
            $data = array_diff_key($dataList, $ignoreList);

            foreach ($data as $val) {
                if (!is_numeric($val)) {
                    $flag = false;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($data) . ' Need To be Number But Invalid Number Supplied');
                    break;
                }
            }
        } else {
            if (!is_numeric($dataList)) {
                $flag = false;
                parent::setSuccess(false);
                parent::setMsg(json_encode($dataList) . ' Need To be Number But Invalid Number Supplied');
            }
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
                parent::setMsg(json_encode($lookUpDataArr) . ' Are Required But Required Fields Not Supplied');
                break;
            }
        }

        return $flag;
    }

    public static function isDate($date)
    {
        $flag = true;

        if (is_array($date)) {
            foreach ($date as $dt) {
                if (!strtotime($dt)) {
                    $flag = false;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($data) . ' Need To Be A Date But No Valid Date Supplied');
                    break;
                }
            }
        } else {
            if (!strtotime($date)) {
                $flag = false;
                parent::setSuccess(false);
                parent::setMsg(json_encode($data) . ' Need To Be A Date But No Valid Date Supplied');
            }
        }

        return $flag;
    }

}
