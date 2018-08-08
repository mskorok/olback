<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class ProcessUsersOld extends DateTrackingModel
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
