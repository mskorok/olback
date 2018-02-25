<?php

namespace App\Model;

class ProcessDepartments  extends \App\Mvc\DateTrackingModel
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
