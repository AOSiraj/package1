<?php namespace package1\Controller\Action;

/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/7/2017
 * Time: 12:01 PM
 */


use Common\Controller\DriAbstractActionController;
use package1\Service\UtilityService;
use Zend\View\Model\JsonModel;

class MyActionsController  extends DriAbstractActionController
{

    /**
     * Collects survey details from SurveyMonkey and stores them in db
     * @return JsonModel
     */
    public function loadSurveyDetailsFromSurveyMonkeyToDatabaseAction(){
        $response = array(
            'surveyLoading'=>null,
            'collectorLoading'=>null
        );

        //fetch and store survey details from SurveyMonekey
        $SurveyMonkeySurveyService = $this->getServiceLocator()->get('package1\Service\SurveyMonkey\SurveyMonkeySurveyService');
        $response['surveyLoading'] = $SurveyMonkeySurveyService->getAllSurveyDetailsFromSurveyMonkeyAndSaveToDB();

        //collect survey list from db and fetch collector information from SurveyMonkey for each survey
        $SurveyService = $this->getServiceLocator()->get('package1\Service\SurveyService');
        $surveys = $SurveyService->getAllSurveysFromSurveyTable();
        $CollectorService = $this->getServiceLocator()->get('package1\Service\SurveyMonkey\CollectorService');
        $CollectorService->deleteAllCollectors();
        $response['collectorLoaded'] = $CollectorService->getAllCollectorsFromSurveyMonkeyAndSaveToDB($surveys);

        return new JsonModel(UtilityService::getHttpRequestCountToSurveyMonkey($response));
    }


    /**
     * Collects survey Responses from SurveyMonkey and stores them in db
     * @return JsonModel
     */
    public function loadSurveyResultsFromSurveyMonkeyToDatabaseAction(){
        $response = array(
            'SurveyResponse' => null,
            'SurveyResponseProcessing' => null
        );

        UtilityService::setUserInfo(
            $this->getServiceLocator()->get('Auth\Service\AuthService'),
            $this->getServiceLocator()->get('Auth\Model\Ims\Users'),
            $this->getServiceLocator()->get('Auth\Model\Ims\UsersTable')
        );

        // collect responses for each survey
        $ResponsesService = $this->getServiceLocator()->get('package1\Service\SurveyMonkey\ResponsesService');
        $response['SurveyResponse'] = $ResponsesService->getAllSurveyResponseFromSurveyMonkeyAndSaveToDB();

        //consume collected responses and map with job_item_id
        $SurveyResponseListService = $this->getServiceLocator()->get('package1\Service\SurveyResponseListService');
        $response['SurveyResponseProcessing'] = $SurveyResponseListService->ProcessAndSaveResponsesToResponseList();

        return new JsonModel(UtilityService::getHttpRequestCountToSurveyMonkey($response));
    }


    /**
     * Collects customer list for sending survey email and
     * scrub agiinst the scriblist and stores the list in db
     * @return JsonModel
     */
    public function gatherSurveyCandidatesAction(){
        UtilityService::setUserInfo(
            $this->getServiceLocator()->get('Auth\Service\AuthService'),
            $this->getServiceLocator()->get('Auth\Model\Ims\Users'),
            $this->getServiceLocator()->get('Auth\Model\Ims\UsersTable')
        );

        // get survey list from db
        $SurveyService = $this->getServiceLocator()->get('package1\Service\SurveyService');
        $Surveys = $SurveyService->getAllSurveysFromSurveyTable();

        // collect survey candidates for specific period in db and
        // scrub against survey_scrub_list table
        $SurveyCandidatesNewService = $this->getServiceLocator()->get('package1\Service\SurveyCandidatesNewService');
        $response = array();

        //collect logging information
        foreach ($Surveys as $Survey){
            $SurveyCandidatesNewService->gatherSurveyCandidates($Survey);
            $pollingTime = $SurveyService->UpdateLastCandidatePollDateTime($Survey['survey_id']);
            array_push($response, array(
                'survey_id'=>$Survey['survey_source_foreign_id'],
                'polling_date_time' => $pollingTime,
                'candidates' => $SurveyCandidatesNewService->getAllSurveyCandidatesWithAttributeData($Survey['survey_source_foreign_id'])));
        }

        return new JsonModel(UtilityService::getHttpRequestCountToSurveyMonkey($response));
    }


    /**
     * Sends emails to collected recipients
     * @return JsonModel
     */
    public function sendRequestEmailAction(){
        try{
            $EmailInvitationService = $this->getServiceLocator()->get('package1\Service\EmailInvitationService');
            $willSendMockEmailForQA = isset($this->getServiceLocator()->get('config')['messaging_server']['mockEmailAddress']);
            $response =  $EmailInvitationService->SendEmail($willSendMockEmailForQA);
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }

        return new JsonModel(UtilityService::getHttpRequestCountToSurveyMonkey($response));
    }

}