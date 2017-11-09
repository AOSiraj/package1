<?php namespace package1\Model\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/9/2017
 * Time: 4:12 PM
 */


class Questions
{
    public $question_id;
    public $page_id;
    public $sorting;
    public $family;
    public $subtype;
    public $visible;
    public $href;
    public $position;
    public $validation;
    public $forced_ranking;
    public $required;
    public $headings;
    public $date_inserted;
    public $date_updated;


    /**
     * @param array $data
     */
    public  function exchangeArray(array $data){
        $this->question_id = !empty($data['question_id'])? $data['question_id'] : null;
        $this->page_id = !empty($data['page_id'])? $data['page_id'] : null;
        $this->sorting = !empty($data['sorting'])? $data['sorting'] : null;
        $this->family = !empty($data['family'])? $data['family'] : null;
        $this->subtype = !empty($data['subtype'])? $data['subtype'] : null;
        $this->visible = !empty($data['visible'])? $data['visible'] : null;
        $this->href = !empty($data['href'])? $data['href'] : null;
        $this->headings = !empty($data['headings'])? $data['headings'] : null;
        $this->position = !empty($data['position'])? $data['position'] : null;
        $this->validation = !empty($data['validation'])? $data['validation'] : null;
        $this->forced_ranking = !empty($data['forced_ranking'])? $data['forced_ranking'] : null;
        $this->required = !empty($data['required'])? $data['required'] : null;
        $this->date_inserted = !empty($data['date_inserted'])? $data['date_inserted'] : null;
        $this->date_updated = !empty($data['date_updated'])? $data['date_updated'] : null;
    }
}