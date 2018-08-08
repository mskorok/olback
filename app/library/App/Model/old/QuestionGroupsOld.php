<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class QuestionGroupsOld extends DateTrackingModel
{
    public $id;
    public $name;

    public function getSource()
    {
        return 'question_group';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'name' => 'name'
        ];
    }
}
