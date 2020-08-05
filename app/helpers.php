<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 12-11-2018
 * Time: 13:11
 */


/**
 * * @param $string
 * @return bool
 */
function checkBool($string)
{
    $string = strtolower($string);
    return in_array($string, array('true', 'false', '1', '0', 'yes', 'no'), true);
}

function convertBoolStringToInt($string)
{
    $string = strtolower($string);

    if (in_array($string, array('true', '1', 'yes', 'on'), true)) {
        return 1;
    }

    if (in_array($string, array('false', '0', 'no', 'off'), true)) {
        return 0;
    }
    return 0;
}
function convertIntToBool($int )
{
    if($int === 1) {
        return true;
    }
    if($int === 0) {
        return false;
    }
}

function isAssoc(array $arr)
{
    if (array() === $arr) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function shortenedString(string $str, int $len, $trailing = true)
{
    if (strlen($str) > $len) {
        if ($trailing === true) {
            return substr($str, 0, $len - 3) . '...';
        }
        return substr($str, 0, $len);
    }
    return $str;
}

function PrettyDateFormat( $date) {
    if($date !== null){
    return date('d M H:m', strtotime($date));
    }

    return '';

}
function DBDateFormat(string $date) {
    //02 Jul 13:15
    return date('Y-m-d H:m:s', strtotime($date));
}
function isPast(string $date) {
    date_default_timezone_set('Europe/Copenhagen');

    return strtotime($date) < strtotime(date('m/d/Y h:i:s'));
}
