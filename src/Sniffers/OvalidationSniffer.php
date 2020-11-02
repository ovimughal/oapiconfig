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

    public static function isEmpty($dataList, $ignoreListRaw = null)
    {
        $res = false;
        if (is_array($dataList)) {

            if (null != $ignoreListRaw) {
                $ignoreList = array_flip($ignoreListRaw);
                $data = array_diff_key($dataList, $ignoreList);
            } else {
                $data = $dataList;
            }

            foreach ($data as $key => $val) {
                if ('' === $val) {
                    $emptyData[] = $key;
                    $res = true;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($emptyData) . ' Cannot Be Empty But Required Fields Are Empty');
                }
            }
        } else {
            if ('' === $dataList) {
                $res = true;
                parent::setSuccess(false);
                parent::setMsg(json_encode($dataList) . ' Cannot Be Empty But Required Fields Are Empty');
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

            if (null != $ignoreListRaw) {
                $ignoreList = array_flip($ignoreListRaw);
                $data = array_diff_key($dataList, $ignoreList);
            } else {
                $data = $dataList;
            }

            foreach ($data as $key => $val) {
                if (!is_int($val)) {
                    $notInt[] = $key;
                    $flag = false;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($notInt) . ' Need To be Integer But Invalid Integer Supplied');
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

            if (null != $ignoreListRaw) {
                $ignoreList = array_flip($ignoreListRaw);
                $data = array_diff_key($dataList, $ignoreList);
            } else {
                $data = $dataList;
            }

            foreach ($data as $key => $val) {
                if (!is_numeric($val)) {
                    $notNumeric[] = $key;
                    $flag = false;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($notNumeric) . ' Need To be Number But Invalid Number Supplied');
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
                $missingLookUp[] = $lookUp;
                $flag = false;
                parent::setSuccess(false);
                parent::setMsg(json_encode($missingLookUp) . ' Are Required But Required Fields Not Supplied');
            }
        }

        return $flag;
    }

    public static function isDate($date)
    {
        $flag = true;

        if (is_array($date)) {
            foreach ($date as $key => $dt) {
                if (!strtotime($dt)) {
                    $notDate[] = $key;
                    $flag = false;
                    parent::setSuccess(false);
                    parent::setMsg(json_encode($notDate) . ' Need To Be A Date But No Valid Date Supplied');
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

    public static function getNumbers($dataList, $toObtainNumbersForList = null, $numberSign = 'positive', $decimalOp = 'rounding')
    {
        $globalProc = new \GlobalProcedure\Model\GlobalProcedureModel();
        $userData = \Oapiconfig\DI\ServiceInjector::oJwtizer()->getUserInfo();
        $userAuthenticationCode = $userData['userId'];
        $userPrefrences = $globalProc->getUserPrefrences($userAuthenticationCode);
        $decimalScalePrecision = $userPrefrences['decimalscale'];

        if (is_array($dataList)) {
            if (null != $toObtainNumbersForList) {
                $numbersForList = array_flip($toObtainNumbersForList);
                $data = array_intersect_key($dataList, $numbersForList);
            } else {
                $data = $dataList;
            }

            $pureNumberList = [];
            $pureNumber = 0;
            foreach ($data as $key => $val) {
                if ('negative' === $numberSign) {
                    $pureNumber = preg_replace('/(?!^)-/', '', preg_replace('/(\..*)\./', '$1', preg_replace('/[^0-9.-]/', '', $val)));

                    if ('rounding' === $decimalOp) {
                        $pureNumberList[$key] = round($pureNumber, $decimalScalePrecision);
                    } else if ('norounding' === $decimalOp) {
                        $pureNumberList[$key] = $pureNumber;
                    }
                } else {
                    $pureNumber = preg_replace("/[^0-9\.]/", "", $val);

                    if ('rounding' === $decimalOp) {
                        $pureNumberList[$key] = round($pureNumber, $decimalScalePrecision);
                    } else if ('norounding' === $decimalOp) {
                        $pureNumberList[$key] = $pureNumber;
                    }
                }
            }

            $numbers = $pureNumberList;
        } else {
            if ('negative' === $numberSign) {
                $pureNumber = preg_replace('/(?!^)-/', '', preg_replace('/(\..*)\./', '$1', preg_replace('/[^0-9.-]/', '', $dataList)));
//                $numbers = round($pureNumber, $decimalScalePrecision);
                if ('rounding' === $decimalOp) {
                    $numbers = round($pureNumber, $decimalScalePrecision);
                } else if ('norounding' === $decimalOp) {
                    $numbers = $pureNumber;
                }
            } else {
                $pureNumber = preg_replace("/[^0-9\.]/", "", $dataList);
//                $numbers = round($pureNumber, $decimalScalePrecision);
                if ('rounding' === $decimalOp) {
                    $numbers = round($pureNumber, $decimalScalePrecision);
                } else if ('norounding' === $decimalOp) {
                    $numbers = $pureNumber;
                }
            }
        }

        return $numbers;
    }

}
