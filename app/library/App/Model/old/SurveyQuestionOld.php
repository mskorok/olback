<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class SurveyQuestionOld extends DateTrackingModel
{
    public $id;
    public $question;
    public $description;
    public $answered_type;
    public $question_order;
    public $survey_id;
    public $question_group_id;

    public function getSource()
    {
        return 'survey_questions';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'question' => 'question',
                'description' => 'description',
                'answered_type' => 'answered_type',
                'question_order' => 'question_order',
                'survey_id' => 'survey_id',
                'question_group_id' => 'question_group_id'
            ];
    }
}
