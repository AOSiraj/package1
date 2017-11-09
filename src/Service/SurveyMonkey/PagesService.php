<?php namespace package1\Service\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/16/17
 * Time: 4:21 PM
 */


use package1\Service\UtilityService;

class PagesService
{

    protected $QuestionsService;
    protected $PagesTable;
    function __construct($QuestionsService, $PagesTable){
        $this->QuestionsService = $QuestionsService;
        $this->PagesTable = $PagesTable;

    }

    /**
     * @param $pages
     * @param $survey_id
     * @return bool|null
     */
    public function SavePagesToDatabase($pages, $survey_id){
        try {
            foreach ($pages as $page) {
                $page['survey_id'] = $survey_id;

                if (ISSET($page['questions']) && $page['questions'] != NULL) {
                    $this->QuestionsService->SaveQuestionsToDatabase($page['questions'], $page['id']);
                }

                $this->SaveSinglePageToDatabase($page);
            }
        }catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
        return true;
    }

    /**
     * @param $pageDetails
     * @return bool|null
     */
    public function SaveSinglePageToDatabase($pageDetails)
    {
        try {
            $newEntry['page_id'] = $pageDetails['id'];
            $newEntry['survey_id'] = $pageDetails['survey_id'];
            $newEntry['description'] = $pageDetails['description'];
            $newEntry['title'] = $pageDetails['title'];
            $newEntry['position'] = $pageDetails['position'];
            $newEntry['question_count'] = $pageDetails['question_count'];
            $newEntry['href'] = $pageDetails['href'];

            $oldEntry = $this->PagesTable->getColumnById(array('page_id' => $newEntry['page_id']));
            if (isset($oldEntry)) {
                $this->PagesTable->update($newEntry, array('page_id' => $newEntry['page_id']));
            } else {
                $this->PagesTable->insert($newEntry, array('page_id' => $newEntry['page_id']));
            }
        }catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
        return true;
    }
}