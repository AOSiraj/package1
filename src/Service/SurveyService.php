<?php namespace package1\Service;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/29/17
 * Time: 9:08 PM
 */


class SurveyService
{
    protected $SurveyTable;

    /**
     * SurveyService constructor.
     * @param $SurveyTable
     */
    public function __construct($SurveyTable){
        $this->SurveyTable = $SurveyTable;
    }

    /**
     * @return mixed
     */
    public function getAllSurveysFromSurveyTable(){
        return $this->SurveyTable->fetchAll();
    }

    /**
     * @param $SurveyId
     * @return mixed
     */
    public function UpdateLastCandidatePollDateTime($SurveyId){
        try {
            $updated = UtilityService::getUTCDateTime();
            $valuesToUpdate = array(
                'last_candidate_poll_datetime' => $updated,
                'updated_by' => UtilityService::getUserInfo()->user_id
                );

            $this->SurveyTable->update($valuesToUpdate ,array('survey_id'=>$SurveyId));
            return $updated;
        } catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

}