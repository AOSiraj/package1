<?php namespace package1\Model;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/29/17
 * Time: 8:55 PM
 */


class SurveyScrubList
{
    public $survey_id;
    public $customer_id;
    public $website_code;
    public $last_sent;
    public $last_responded;
    public $date_inserted;
    public $date_updated;

    /**
     * @param array $data
     */
    public  function exchangeArray(array $data){
        $this->survey_id = !empty($data['survey_id'])? $data['survey_id'] : null;
        $this->customer_id = !empty($data['customer_id'])? $data['customer_id'] : null;
        $this->website_code = !empty($data['website_code'])? $data['website_code'] : null;
        $this->last_sent = !empty($data['last_sent'])? $data['last_sent'] : null;
        $this->last_responded = !empty($data['last_responded'])? $data['last_responded'] : null;
        $this->date_inserted = !empty($data['date_inserted'])? $data['date_inserted'] : null;
        $this->date_updated = !empty($data['date_updated'])? $data['date_updated'] : null;
    }
}