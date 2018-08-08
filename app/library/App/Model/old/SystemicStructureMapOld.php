<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class SystemicStructureMapOld extends DateTrackingModel
{
    public $id;
    public $name;
    public $by_whom;
    public $organization;
    public $isActive;
    public $lang;
    public $processId;
    public $startDate;
    public $endDate;

    public function getSource()
    {
        return 'systemic_structure_map';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'name' => 'name',
                'by_whom' => 'by_whom',
                'organization' => 'organization',
                'lang' => 'lang',
                'isActive' => 'isActive',
                'processId' => 'processId',
                'startDate' => 'startDate',
                'endDate' => 'endDate'
            ];
    }
}
