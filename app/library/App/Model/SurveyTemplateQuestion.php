<?php

namespace App\Model;

class SurveyTemplateQuestion extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $question;
    public $description;
    public $answered_type;
    public $question_order;
    public $survey_id;

    public function getSource()
    {
        return 'survey_templates_questions';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'question' => 'question',
            'description' => 'description',
            'answered_type'=>'answered_type',
            'question_order' => 'question_order',
            'survey_id' => 'survey_id'
        ];
    }
}
