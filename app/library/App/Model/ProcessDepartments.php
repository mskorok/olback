<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class ProcessDepartments extends DateTrackingModel
{
    public $id;
    public $processId;
    public $departmentId;

    public function getSource()
    {
        return 'process_departments';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'processId' => 'processId',
                'departmentId' => 'departmentId'
            ];
    }
}
