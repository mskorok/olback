<?php

namespace App\Model;

class SystemicStructureMap extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $name;
    public $byWhom;
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
            'byWhom' => 'by_whom',
            'organization'=>'organization',
            'lang' => 'lang',
            'isActive'=>'isActive',
            'processId'=>'processId',
                'startDate'=>'startDate',
                'endDate'=>'endDate'
        ];
    }
}
