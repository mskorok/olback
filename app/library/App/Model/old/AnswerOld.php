<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class AnswerOld extends DateTrackingModel
{
    public $id;
    public $questionId;
    public $answer;
    public $userId;

    public function getSource()
    {
        return 'answers';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'questionId' => 'questionId',
                'answer' => 'answer',
                'userId'=>'userId',
            ];
    }
}
