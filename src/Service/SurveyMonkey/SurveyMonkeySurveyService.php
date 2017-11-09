<?php namespace package1\Service\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/3/2017
 * Time: 3:26 PM
 */

use package1\Service\UtilityService;

class SurveyMonkeySurveyService
{

    protected $HttpRequestService;
    protected $SurveysTable;
    protected $PagesService;
    protected $SurveyService;

    /**
     * SurveyMonkeySurveyService constructor.
     * @param $HttpRequestService
     * @param $SurveysTable
     * @param $PagesService
     * @param $SurveyService
     */
    function __construct($HttpRequestService, $SurveysTable, $PagesService, $SurveyService)
    {
        $this->HttpRequestService = $HttpRequestService;
        $this->SurveysTable = $SurveysTable;
        $this->PagesService = $PagesService;
        $this->SurveyService = $SurveyService;
    }

    /**
     * @return null
     */
    public function getSurveysFromSurveyMonkey(){
        try {
            $requestUri = "https://api.surveymonkey.net/v3/surveys?include=date_modified,date_created";
            $response = $this->HttpRequestService->get($requestUri);
            return $response;
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

    /**
     * @param $surveyId
     * @return null
     */
    public function getSurveyDetailsFromSurveyMonkey($surveyId){
        try {
            $requestUri = "https://api.surveymonkey.net/v3/surveys/" . $surveyId . "/details";
            $response = $this->HttpRequestService->get($requestUri);
            return $response;
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

    /**
     * @return array|null|string
     */
    public function getAllSurveyDetailsFromSurveyMonkeyAndSaveToDB(){
        try {
            $surveys = $this->getSurveysFromSurveyMonkey();
            if ($surveys == NULL || !ISSET($surveys)) {
                return array("error" => "No Survey data Returned from SurveyMonkey");
            }

            $surveysToLoadFromSurveyMonkey = $this->SurveyService->getAllSurveysFromSurveyTable();
            $updatedSurveyList = array();
            foreach ($surveysToLoadFromSurveyMonkey as $survey) {
                $id = array_search($survey['survey_source_foreign_id'], array_column($surveys['data'], 'id'));
                if ($id !== false) {
                    array_push($updatedSurveyList, $surveys['data'][$id]);
                } else {
                    return "survey entry is wrong!!. No survey Found with id: " . $survey['survey_source_foreign_id'];
                }

            }

            return $this->SaveSurveysToDatabase($updatedSurveyList);
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

    /**
     * @param $surveys
     * @return array|null
     */
    public function SaveSurveysToDatabase($surveys){
        try {
            $inserted = array();
            $updated = array();
            $unchanged = array();
            $total_surveys_received = array();
            foreach ($surveys as $survey) {
                $oldEntry = $this->SurveysTable->getRowById(array('survey_id' => $survey['id']));
                if ($oldEntry == null || UtilityService::CheckIfUpdated($survey['date_modified'], $oldEntry['date_modified'])) {
                    $surveyDetails = $this->getSurveyDetailsFromSurveyMonkey($survey['id']);
                    if ($surveyDetails['pages'] == NULL || !ISSET($surveyDetails['pages'])) {
                        return array("error" => "No Survey pages is Survey data");
                    }

                    $this->PagesService->SavePagesToDatabase($surveyDetails['pages'], $surveyDetails['id']);

                    $responseSurvey = $this->SaveSingleSurveyToDatabase($surveyDetails, $oldEntry);

                    (isset($responseSurvey['insert']) && $responseSurvey['insert']) ? array_push($inserted, $survey) : null;
                    (isset($responseSurvey['update']) && $responseSurvey['update']) ? array_push($updated, $survey) : null;
                } else {
                    array_push($unchanged, $survey);
                }
                array_push($total_surveys_received, $survey);
            }
            $response = array(
                "total_surveys_received" => $total_surveys_received,
                "survey_inserted" => $inserted,
                "survey_updated" => $updated,
                "unchanged" => $unchanged,
            );

            return $response;
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

    /**
     * @param $surveyDetails
     * @param $oldEntry
     * @return array|null
     */
    public function SaveSingleSurveyToDatabase($surveyDetails, $oldEntry)
    {
        try {
            $newEntry['survey_id'] = $surveyDetails['id'];
            $newEntry['category'] = json_encode($surveyDetails['category']);
            $newEntry['custom_variables'] = json_encode($surveyDetails['custom_variables']);
            $newEntry['edit_url'] = $surveyDetails['edit_url'];
            $newEntry['title'] = $surveyDetails['title'];
            $newEntry['nickname'] = $surveyDetails['nickname'];
            $newEntry['href'] = $surveyDetails['href'];
            $newEntry['response_count'] = $surveyDetails['response_count'];
            $newEntry['page_count'] = $surveyDetails['page_count'];
            $newEntry['date_created'] = $surveyDetails['date_created'];
            $newEntry['folder_id'] = $surveyDetails['folder_id'];
            $newEntry['question_count'] = $surveyDetails['question_count'];
            $newEntry['preview'] = $surveyDetails['preview'];
            $newEntry['is_owner'] = $surveyDetails['is_owner'];
            $newEntry['language'] = $surveyDetails['language'];
            $newEntry['footer'] = $surveyDetails['footer'];
            $newEntry['date_modified'] = $surveyDetails['date_modified'];
            $newEntry['analyze_url'] = $surveyDetails['analyze_url'];
            $newEntry['summary_url'] = $surveyDetails['summary_url'];
            $newEntry['collect_url'] = $surveyDetails['collect_url'];

            if (isset($oldEntry)) {
                $newEntry['last_poll_datetime'] = UtilityService::getUTCDateTime();
                $response = $this->SurveysTable->update($newEntry, array('survey_id' => $newEntry['survey_id']));
                if (!$response)
                    return null;
                else
                    return array("update" => true);
            } else {
                $newEntry['last_poll_datetime'] = UtilityService::getUTCDateTime();
                $this->SurveysTable->insert($newEntry, array('survey_id' => $newEntry['survey_id']));
                return array("insert" => true);
            }
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

    /**
     * @return null
     */
    public function getAllSurveysFromDatabase(){
        try {
            return $this->SurveysTable->fetchAll();
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
    }

}