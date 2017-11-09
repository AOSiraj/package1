<?php namespace package1\Model\SurveyMonkey;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/9/2017
 * Time: 2:07 PM
 */


class Surveys
{
    public $survey_id;
	public $category;
	public $edit_url;
	public $title;
	public $nickname;
	public $href;
	public $response_count;
	public $page_count;
	public $date_created;
	public $folder_id;
	public $custom_variables;
	public $question_count;
	public $preview;
	public $is_owner;
	public $language;
	public $footer;
	public $date_modified;
	public $analyze_url;
	public $summary_url;
	public $collect_url;
	public $last_poll_datetime;
	public $date_inserted;
	public $date_updated;

    /**
     * @param array $data
     */
    public  function exchangeArray(array $data){
        $this->survey_id = !empty($data['survey_id'])? $data['survey_id'] : null;
        $this->category = !empty($data['category'])? $data['category'] : null;
        $this->edit_url = !empty($data['edit_url'])? $data['edit_url'] : null;
        $this->title = !empty($data['title'])? $data['title'] : null;
        $this->nickname = !empty($data['nickname'])? $data['nickname'] : null;
        $this->href = !empty($data['href'])? $data['href'] : null;
        $this->response_count = !empty($data['response_count'])? $data['response_count'] : null;
        $this->page_count = !empty($data['page_count'])? $data['page_count'] : null;
        $this->date_created = !empty($data['date_created'])? $data['date_created'] : null;
        $this->folder_id = !empty($data['folder_id'])? $data['folder_id'] : null;
        $this->custom_variables = !empty($data['custom_variables'])? $data['custom_variables'] : null;
        $this->question_count = !empty($data['question_count'])? $data['question_count'] : null;
        $this->preview = !empty($data['preview'])? $data['preview'] : null;
        $this->is_owner = !empty($data['is_owner'])? $data['is_owner'] : null;
        $this->language = !empty($data['language'])? $data['language'] : null;
        $this->footer = !empty($data['footer'])? $data['footer'] : null;
        $this->date_modified = !empty($data['date_modified'])? $data['date_modified'] : null;
        $this->analyze_url = !empty($data['analyze_url'])? $data['analyze_url'] : null;
        $this->summary_url = !empty($data['summary_url'])? $data['summary_url'] : null;
        $this->collect_url = !empty($data['collect_url'])? $data['collect_url'] : null;
        $this->last_poll_datetime = !empty($data['last_poll_datetime'])? $data['last_poll_datetime'] : null;
        $this->date_inserted = !empty($data['date_inserted'])? $data['date_inserted'] : null;
        $this->date_updated = !empty($data['date_updated'])? $data['date_updated'] : null;
    }
}