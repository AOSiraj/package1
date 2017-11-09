<?php namespace package1\Model;
/**
 * Created by PhpStorm.
 * User: abdullah.s
 * Date: 8/29/17
 * Time: 8:55 PM
 */


use package1\Model\SurveyMonkey\SurveyMonkeyTable;

class SurveyScrubListTable extends SurveyMonkeyTable
{

    /**
     * @param $send_frequency_types
     * @param $survey_id
     * @return array|ø
     */
    public function ListAlreadySentCandidates($send_frequency_types, $survey_id){
        $sql = $this->tableGateway->getSql();
        $select = $sql->select();
        $select->where("survey_id = ".$survey_id." AND ((last_sent + INTERVAL ".$send_frequency_types['send_frequency_cap_count']
            ." ".$send_frequency_types['survey_frequency_description'].") > NOW())");

        $already_sent = iterator_to_array($this->tableGateway->selectWith($select)->getDataSource());
        return $already_sent;
    }

    /**
     * @return array|ø
     */
    public function getSurvey(){
        $sql = $this->tableGateway->getSql();
        $select = $sql->select();
        return iterator_to_array($this->tableGateway->selectWith($select)->getDataSource());
    }

    /**
     * @param $take_frequency_types
     * @param $survey_id
     * @return array|ø
     */
    public function ListAlreadyRespondedCandidates($take_frequency_types, $survey_id){
        $sql = $this->tableGateway->getSql();
        $select = $sql->select();
        $select->where("survey_id = ".$survey_id." AND (((last_responded + INTERVAL ".$take_frequency_types['take_frequency_cap_count']." ".$take_frequency_types['survey_frequency_description'].") > NOW()))");

        $responded = iterator_to_array($this->tableGateway->selectWith($select)->getDataSource());

        return $responded;
    }

}