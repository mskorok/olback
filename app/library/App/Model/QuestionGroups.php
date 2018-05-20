<?php

namespace App\Model;

class QuestionGroups extends \App\Mvc\DateTrackingModel
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
