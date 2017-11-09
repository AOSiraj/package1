<?php namespace package1\Service;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/25/17
 * Time: 11:00 AM
 */

class HttpRequestListnerService
{
    protected static $httpRequestCount = 1;
    protected static $httpRequestArray = array();

    /**
     * @param $uri
     */
    public static function varDumpRequest($uri){
        array_push(self::$httpRequestArray,"API Call # ".self::$httpRequestCount.", uri: ".$uri);
        self::$httpRequestCount++;
    }

    /**
     *
     */
    public static function reset()
    {
        self::$httpRequestCount = 1;
        self::$httpRequestArray = array();
    }

    /**
     * @return array
     */
    public static function getHttpRequestArray()
    {
        return self::$httpRequestArray;
    }

    /**
     * @return int
     */
    public static function getHttpRequestCount()
    {
        return self::$httpRequestCount;
    }
}