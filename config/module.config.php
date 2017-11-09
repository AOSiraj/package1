<?php
return array(
    'router' => array(
        'routes' => array(
            'load-survey-details' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/load-survey-details',
                    'defaults' => array(
                        'controller' => 'package1\Controller\Action\MyActions',
                        'action'     => 'loadSurveyDetailsFromSurveyMonkeyToDatabase',
                    )
                )
            ),
            'load-survey-responses' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/load-survey-responses',
                    'defaults' => array(
                        'controller' => 'package1\Controller\Action\MyActions',
                        'action'     => 'loadSurveyResultsFromSurveyMonkeyToDatabase',
                    )
                )
            ),
            'gather-survey-candidates' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/gather-survey-candidates',
                    'defaults' => array(
                        'controller' => 'package1\Controller\Action\MyActions',
                        'action'     => 'gatherSurveyCandidates',
                    )
                )
            ),
            'send-request-email' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/send-request-email',
                    'defaults' => array(
                        'controller' => 'package1\Controller\Action\MyActions',
                        'action'     => 'sendRequestEmail',
                    )
                )
            ),
            'survey-responses' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/survey-responses[/:id]',
                    'defaults' => array(
                        'controller' => 'package1\Controller\Rest\SurveyResponse',
                        'constraints' => [
                            'id' => '[0-9A-z-+_]*'
                        ]
                    )
                )
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'package1\Controller\Rest\SurveyResult'   => 'package1\Controller\Rest\SurveyResultController',
            'package1\Controller\Action\MyActions'   => 'package1\Controller\Action\MyActionsController',
            'package1\Controller\Rest\SurveyResponse' => 'package1\Controller\Rest\SurveyResponseController'
        ),
    ),
    'view_manager' => array(
            'template_map' => array(
                'EMAIL_TEMPLATE_UPRINTING'                => __DIR__ . '/../view/EmailTemplateUPrinting.phtml',
        ),
    ),
);