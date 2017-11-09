<?php namespace package1\Service\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/16/17
 * Time: 4:02 PM
 */


use package1\Service\UtilityService;

class QuestionsService
{
    protected $AnswerRowsService;
    protected $ChoicesService;
    protected $QuestionsTable;
    function __construct($AnswerRowsService, $ChoicesService, $QuestionsTable){
        $this->AnswerRowsService = $AnswerRowsService;
        $this->ChoicesService = $ChoicesService;
        $this->QuestionsTable = $QuestionsTable;
    }

    /**
     * @param $questions
     * @param $page_id
     * @return bool|null
     */
    public function SaveQuestionsToDatabase($questions, $page_id)
    {
        try {
            foreach ($questions as $question) {
                $question['page_id'] = $page_id;
                if (ISSET($question['answers']['rows']) && $question['answers']['rows'] != NULL)
                    $this->AnswerRowsService->SaveAnswersToDatabase($question['answers']['rows'], $question['id']);
                if (ISSET($question['answers']['choices']) && $question['answers']['choices'] != NULL)
                    $this->ChoicesService->SaveChoicesToDatabase($question['answers']['choices'], $question['id']);

                $this->SaveSingleQuestionToDatabase($question);
            }
        } catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
        return true;
    }

    /**
     * @param $questionDetails
     * @return bool|null
     */
    public function SaveSingleQuestionToDatabase($questionDetails)
    {
        try {
            $newEntry['question_id'] = $questionDetails['id'];
            $newEntry['page_id'] = $questionDetails['page_id'];
            $newEntry['sorting'] = $questionDetails['sorting'];
            $newEntry['family'] = $questionDetails['family'];
            $newEntry['subtype'] = $questionDetails['subtype'];
            $newEntry['visible'] = $questionDetails['visible'];
            $newEntry['href'] = $questionDetails['href'];
            $newEntry['headings'] = json_encode($questionDetails['headings']);
            $newEntry['position'] = $questionDetails['position'];
            $newEntry['validation'] = $questionDetails['validation'];
            $newEntry['forced_ranking'] = $questionDetails['forced_ranking'];
            $newEntry['required'] = json_encode($questionDetails['required']);


            $oldEntry = $this->QuestionsTable->getColumnById(array('question_id' => $newEntry['question_id']));
            if (isset($oldEntry)) {
                $this->QuestionsTable->update($newEntry, array('question_id' => $newEntry['question_id']));
            } else {
                $this->QuestionsTable->insert($newEntry, array('question_id' => $newEntry['question_id']));
            }
        }
        catch (\Exception $exception){
            return UtilityService::outputExceptionMessage($exception); // it returns null
        }
        return true;
    }
}