<?php
namespace package1;

use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;

use package1\Service\HttpRequestService;
use package1\Service\AuthenticationService;
use package1\Service\EmailInvitationService;
use package1\Service\SurveyResponseListService;

use package1\Service\SurveyMonkey\AnswerResponseService;
use package1\Service\SurveyMonkey\AnswerRowsService;
use package1\Service\SurveyMonkey\ChoicesService;
use package1\Service\SurveyMonkey\CollectorService;
use package1\Service\SurveyMonkey\PagesService;
use package1\Service\SurveyMonkey\QuestionsService;
use package1\Service\SurveyMonkey\ResponsesService;
use package1\Service\SurveyMonkey\SurveyMonkeySurveyService;

use package1\Model\SurveyMonkey\AnswerResponse;
use package1\Model\SurveyMonkey\Collectors;
use package1\Model\SurveyMonkey\Responses;
use package1\Model\SurveyMonkey\Surveys;
use package1\Model\SurveyMonkey\AnswerRows;
use package1\Model\SurveyMonkey\Choices;
use package1\Model\SurveyMonkey\Pages;
use package1\Model\SurveyMonkey\Questions;
use package1\Model\SurveyMonkey\SurveyMonkeyTable;
use package1\Model\SurveyMonkey\ResponsesTable;
use package1\Model\SurveyMonkey\ResponsesListTable;
use package1\Model\SurveyMonkey\ResponseAttributes;

use package1\Service\SurveyService;
use package1\Service\SurveyScrubListService;
use package1\Service\SurveyCandidatesNewService;

use package1\Model\SurveyResponseAttributeOptions;
use package1\Model\SurveyResponseAttributes;
use package1\Model\SurveyResponseAttributeValues;
use package1\Service\SurveyResponseAttributeService;

use package1\Model\SurveyResponseList;
use package1\Model\SurveyFrequencyTypes;
use package1\Model\SurveyScrubList;
use package1\Model\SurveyCandidatesNew;
use package1\Model\Survey;
use package1\Model\SurveyCandidateAttributes;
use package1\Model\IMS\Customer;
use package1\Model\IMS\JobItemPackages;
use package1\Model\IMS\JobItemPackagesTable;
use package1\Model\IMS\JobOrders;
use package1\Model\IMS\JobOrdersTable;
use package1\Model\SurveyScrubListTable;


 /**
 * Class Module
 * @author Abdullah Omar Siraj <siraj.zafl@gmail.com>
 * 8/3/2017
 */
class Module
{
    /**
    * @param MvcEvent $e
    */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(

                'package1\Service\SurveyMonkey\SurveyMonkeySurveyService' => function ($sm) {
                    $HttpRequestService = $sm->get('package1\Service\HttpRequestService');
                    $SurveysTable = $sm->get('package1\Model\SurveyMonkey\SurveysTable');
                    $PagesService = $sm->get('package1\Service\SurveyMonkey\PagesService');
                    $SurveyService = $sm->get('package1\Service\SurveyService');
                    return new SurveyMonkeySurveyService($HttpRequestService, $SurveysTable,$PagesService, $SurveyService);
                },
                'package1\Service\HttpRequestService' => function ($sm) {
                    $AuthenticationService = $sm->get('package1\Service\AuthenticationService');
                    return new HttpRequestService($AuthenticationService);
                },
                'package1\Service\AuthenticationService' => function () {
                    return new AuthenticationService();
                },
                'package1\Service\EmailInvitationService' => function ($sm) {
                    $HttpRequestService = $sm->get('package1\Service\HttpRequestService');
                    $SurveyCandidatesNewService = $sm->get('package1\Service\SurveyCandidatesNewService');
                    $CustomerTable = $sm->get('package1\Model\IMS\CustomerTable');
                    $SurveyService = $sm->get('package1\Service\SurveyService');
                    $CollectorService = $sm->get('package1\Service\SurveyMonkey\CollectorService');
                    $AuthService = $sm->get('Auth\Service\AuthService');
                    $messaging_config = $sm->get('config')['messaging_server'];
                    return new EmailInvitationService($HttpRequestService, $SurveyCandidatesNewService, $CustomerTable, $SurveyService, $CollectorService, $AuthService, $messaging_config);
                },
                'package1\Model\SurveyMonkey\SurveysTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\SurveysTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\SurveysTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Surveys());
                    return new TableGateway('surveymonkey_survey', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\AnswerRowsService' => function ($sm) {
                    $AnswerRowsTable = $sm->get('package1\Model\SurveyMonkey\AnswerRowsTable');
                    return new AnswerRowsService($AnswerRowsTable);
                },
                'package1\Model\SurveyMonkey\AnswerRowsTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\AnswerRowsTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\AnswerRowsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AnswerRows());
                    return new TableGateway('surveymonkey_answer_rows', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\ChoicesService' => function ($sm) {
                    $ChoicesTable = $sm->get('package1\Model\SurveyMonkey\ChoicesTable');
                    return new ChoicesService($ChoicesTable);
                },
                'package1\Model\SurveyMonkey\ChoicesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\ChoicesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\ChoicesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Choices());
                    return new TableGateway('surveymonkey_choices', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\PagesService' => function ($sm) {
                    $QuestionsService = $sm->get('package1\Service\SurveyMonkey\QuestionsService');
                    $PagesTable = $sm->get('package1\Model\SurveyMonkey\PagesTable');
                    return new PagesService($QuestionsService, $PagesTable);
                },
                'package1\Model\SurveyMonkey\PagesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\PagesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\PagesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Pages());
                    return new TableGateway('surveymonkey_pages', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\QuestionsService' => function ($sm) {
                    $QuestionsTable = $sm->get('package1\Model\SurveyMonkey\QuestionsTable');
                    $AnswerRowsService = $sm->get('package1\Service\SurveyMonkey\AnswerRowsService');
                    $ChoicesService = $sm->get('package1\Service\SurveyMonkey\ChoicesService');
                    return new QuestionsService($AnswerRowsService, $ChoicesService, $QuestionsTable);
                },
                'package1\Model\SurveyMonkey\QuestionsTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\QuestionsTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\QuestionsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Questions());
                    return new TableGateway('surveymonkey_questions', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\CollectorService' => function ($sm) {
                    $HttpRequestService = $sm->get('package1\Service\HttpRequestService');
                    $CollectorsTable = $sm->get('package1\Model\SurveyMonkey\CollectorsTable');
                    return new CollectorService($HttpRequestService, $CollectorsTable);
                },
                'package1\Model\SurveyMonkey\CollectorsTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\CollectorsTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\CollectorsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Collectors());
                    return new TableGateway('surveymonkey_collector', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\ResponsesService' => function ($sm) {
                    $HttpRequestService = $sm->get('package1\Service\HttpRequestService');
                    $SurveyMonkeySurveyService = $sm->get('package1\Service\SurveyMonkey\SurveyMonkeySurveyService');
                    $CollectorService = $sm->get('package1\Service\SurveyMonkey\CollectorService');
                    $ResponsesTable = $sm->get('package1\Model\SurveyMonkey\ResponsesTable');
                    $AnswerResponseService = $sm->get('package1\Service\SurveyMonkey\AnswerResponseService');
                    $SurveyService = $sm->get('package1\Service\SurveyService');
                    $JobOrdersTable = $sm->get('package1\Model\IMS\JobOrdersTable');
                    $SurveyScrubListService = $sm->get('package1\Service\SurveyScrubListService');
                    $ResponseAttributesTable = $sm->get('package1\Model\SurveyMonkey\ResponseAttributesTable');

                    return new ResponsesService($HttpRequestService, $SurveyMonkeySurveyService, $CollectorService, $ResponsesTable, $AnswerResponseService, $SurveyService, $JobOrdersTable, $SurveyScrubListService, $ResponseAttributesTable);
                },
                'package1\Model\SurveyMonkey\ResponsesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\ResponsesTableGateway');
                    $table = new ResponsesTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\ResponsesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Responses());
                    return new TableGateway('surveymonkey_response', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyMonkey\AnswerResponseService' => function ($sm) {
                    $AnswerResponseTable = $sm->get('package1\Model\SurveyMonkey\AnswerResponseTable');
                    return new AnswerResponseService($AnswerResponseTable);
                },
                'package1\Model\SurveyMonkey\AnswerResponseTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\AnswerResponseTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\AnswerResponseTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AnswerResponse());
                    return new TableGateway('surveymonkey_answer_response', $dbAdapter, null, $resultSetPrototype);
                },

                'package1\Model\SurveyMonkey\ResponseAttributesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\ResponseAttributesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\ResponseAttributesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ResponseAttributes());
                    return new TableGateway('surveymonkey_response_attributes', $dbAdapter, null, $resultSetPrototype);
                },


                /////////////////////// end of Survey Tables And Services
                'package1\Service\SurveyResponseListService' => function ($sm) {
                    $ResponsesService = $sm->get('package1\Service\SurveyMonkey\ResponsesService');
                    $SurveyResponseListTable = $sm->get('package1\Model\SurveyResponseListTable');
                    return new SurveyResponseListService($ResponsesService, $SurveyResponseListTable);
                },
                'package1\Model\SurveyResponseListTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyResponseListTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyResponseListTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyResponseList());
                    return new TableGateway('survey_response_list', $dbAdapter, null, $resultSetPrototype);
                },
                ///////////////////////survey response options and results
                'package1\Model\SurveyMonkey\ResponsesListTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyMonkey\ResponsesListTableGateway');
                    $table = new ResponsesListTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyMonkey\ResponsesListTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyResponseList());
                    return new TableGateway('survey_response_list', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyResponseAttributeService' => function ($sm) {
                    $ResponsesService = $sm->get('package1\Service\SurveyMonkey\ResponsesService');
                    $SurveyResponseAttributesTable = $sm->get('package1\Model\SurveyResponseAttributesTable');
                    $SurveyResponseAttributeValuesTable = $sm->get('package1\Model\SurveyResponseAttributeValuesTable');
                    $SurveyResponseAttributeOptionsTable = $sm->get('package1\Model\SurveyResponseAttributeOptionsTable');
                    $SurveyResponseListTable = $sm->get('package1\Model\SurveyResponseListTable');
                    $JobOrdersTable = $sm->get('package1\Model\IMS\JobOrdersTable');
                    $ResponsesTable = $sm->get('package1\Model\SurveyMonkey\ResponsesListTable');
                    return new SurveyResponseAttributeService($ResponsesService, $SurveyResponseAttributesTable, $SurveyResponseAttributeValuesTable, $SurveyResponseAttributeOptionsTable,$SurveyResponseListTable,$JobOrdersTable,$ResponsesTable);
                },
                'package1\Model\SurveyResponseAttributeOptionsTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyResponseAttributeOptionsTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyResponseAttributeOptionsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyResponseAttributeOptions());
                    return new TableGateway('survey_response_attribute_options', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Model\SurveyResponseAttributesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyResponseAttributesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyResponseAttributesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyResponseAttributes());
                    return new TableGateway('survey_response_attributes', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Model\SurveyResponseAttributeValuesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyResponseAttributeValuesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyResponseAttributeValuesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyResponseAttributeValues());
                    return new TableGateway('survey_response_attribute_values', $dbAdapter, null, $resultSetPrototype);
                },
                //////////////////////////////////////////////////////////
                'package1\Model\IMS\CustomerTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\IMS\CustomerTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\IMS\CustomerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('ims_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Customer());
                    return new TableGateway('customers', $dbAdapter, null, $resultSetPrototype);
                },

                'package1\Model\IMS\JobItemPackagesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\IMS\JobItemPackagesTableGateway');
                    $table = new JobItemPackagesTable($tableGateway);
                    return $table;
                },
                'package1\Model\IMS\JobItemPackagesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('ims_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new JobItemPackages());
                    return new TableGateway('job_item_packages', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Model\IMS\JobOrdersTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\IMS\JobOrdersTableGateway');
                    $table = new JobOrdersTable($tableGateway);
                    return $table;
                },
                'package1\Model\IMS\JobOrdersTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('ims_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new JobOrders());
                    return new TableGateway('job_orders', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyService' => function ($sm) {
                    $SurveyTable = $sm->get('package1\Model\SurveyTable');
                    return new SurveyService($SurveyTable);
                },
                'package1\Model\SurveyTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Survey());
                    return new TableGateway('survey', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyCandidatesNewService' => function ($sm) {
                    $SurveyCandidatesNewTable = $sm->get('package1\Model\SurveyCandidatesNewTable');
                    $SurveyCandidateAttributesTable = $sm->get('package1\Model\SurveyCandidateAttributesTable');
                    $JobItemPackagesTable = $sm->get('package1\Model\IMS\JobItemPackagesTable');
                    $SurveyScrubListService = $sm->get('package1\Service\SurveyScrubListService');
                    $SurveyFrequencyTypesTable = $sm->get('package1\Model\SurveyFrequencyTypesTable');
                    return new SurveyCandidatesNewService($SurveyCandidatesNewTable, $SurveyCandidateAttributesTable, $JobItemPackagesTable, $SurveyScrubListService, $SurveyFrequencyTypesTable);
                },
                'package1\Model\SurveyCandidatesNewTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyCandidatesNewTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyCandidatesNewTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyCandidatesNew());
                    return new TableGateway('survey_candidates_new', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Service\SurveyScrubListService' => function ($sm) {
                    $SurveyScrubListTable = $sm->get('package1\Model\SurveyScrubListTable');
                    return new SurveyScrubListService($SurveyScrubListTable);
                },
                'package1\Model\SurveyScrubListTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyScrubListTableGateway');
                    $table = new SurveyScrubListTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyScrubListTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyScrubList());
                    return new TableGateway('survey_scrub_list', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Model\SurveyFrequencyTypesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyFrequencyTypesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyFrequencyTypesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyFrequencyTypes());
                    return new TableGateway('survey_frequency_types', $dbAdapter, null, $resultSetPrototype);
                },
                'package1\Model\SurveyCandidateAttributesTable' => function ($sm) {
                    $tableGateway = $sm->get('package1\Model\SurveyCandidateAttributesTableGateway');
                    $table = new SurveyMonkeyTable($tableGateway);
                    return $table;
                },
                'package1\Model\SurveyCandidateAttributesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('nps_db');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SurveyCandidateAttributes());
                    return new TableGateway('survey_candidate_attributes', $dbAdapter, null, $resultSetPrototype);
                },



            )
        );
    }
}