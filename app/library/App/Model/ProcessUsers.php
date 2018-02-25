<?php

namespace App\Model;

class ProcessUsers  extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $processId;
    public $userId;

    public function getSource()
    {
        return 'process_users';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'processId' => 'processId',
                'userId' => 'userId'
            ];
    }
}
