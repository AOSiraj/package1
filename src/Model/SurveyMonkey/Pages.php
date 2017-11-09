<?php namespace package1\Model\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/9/2017
 * Time: 4:14 PM
 */


class Pages
{
    public $page_id;
    public $survey_id;
    public $description;
    public $title;
    public $position;
    public $question_count;
    public $href;
    public $date_inserted;
    public $date_updated;


    /**
     * @param array $data
     */
    public  function exchangeArray(array $data){
        $this->page_id = !empty($data['page_id'])? $data['page_id'] : null;
        $this->survey_id = !empty($data['survey_id'])? $data['survey_id'] : null;
        $this->description = !empty($data['description'])? $data['description'] : null;
        $this->title = !empty($data['title'])? $data['title'] : null;
        $this->position = !empty($data['position'])? $data['position'] : null;
        $this->question_count = !empty($data['question_count'])? $data['question_count'] : null;
        $this->href = !empty($data['href'])? $data['href'] : null;
        $this->date_inserted = !empty($data['date_inserted'])? $data['date_inserted'] : null;
        $this->date_updated = !empty($data['date_updated'])? $data['date_updated'] : null;
    }

}