<?php namespace package1\Service;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/25/17
 * Time: 11:34 AM
 */



use package1\Service\HttpRequestListnerService;

class UtilityService
{
    public static $user_info;

    /**
     * @param $newDateFromSUrveyMonkey
     * @param $date_modified_db
     * @return bool
     */
    public static function CheckIfUpdated($newDateFromSUrveyMonkey, $date_modified_db){
        $newDateFromSUrveyMonkey = self::convertToUTCTime($newDateFromSUrveyMonkey);
        $dateFromDBEntry = self::convertToUTCTime($date_modified_db);
        return !($newDateFromSUrveyMonkey == $dateFromDBEntry);
    }

    /**
     * @param $response
     * @return array
     */
    public static function getHttpRequestCountToSurveyMonkey($response){
        return array(
            "response" => $response,
            "QueryDetailsToSurveyMonkeyForThisCall" => HttpRequestListnerService::getHttpRequestArray(),
        );
    }

    /**
     * @param $dateTime
     * @return null|string
     */
    public static function convertToUTCTime($dateTime){
        if($dateTime == null){
            return null;
        }
        $UTC = new \DateTimeZone("UTC");
        $date = new \DateTime($dateTime,$UTC );
        $time = $date->format(DATE_W3C);
        return $time;
    }

    /**
     * @return string
     */
    public static function getUTCDateTime(){
        $UTC = new \DateTimeZone("UTC");;
        $date = new \DateTime(null,$UTC );
        return $date->format(DATE_W3C);
    }

    /**
     * @param \Exception $exception
     * @return null
     */
    public static function outputExceptionMessage(\Exception $exception){
        echo $exception->getMessage().'\n'.get_class($exception);
        return null;
    }

    /**
     * @param $AuthService
     * @param $Users
     * @param $UsersTable
     */
    public static function setUserInfo($AuthService, $Users, $UsersTable){
        $request = $AuthService->getAccessTokenData();
        $user_info = $Users->getUserDetailsByUserId($UsersTable,$request['user_id']);
        self::$user_info = $user_info;
    }

    /**
     * @return mixed
     */
    public static function &getUserInfo()
    {
        return static::$user_info;
    }
}