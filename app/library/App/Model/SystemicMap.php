<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class SystemicMap extends DateTrackingModel
{
    public $id;
    public $name;
    public $department;
    public $organization;
    public $isActive;
    public $lang;
    public $processId;

    public function getSource()
    {
        return 'systemic_map';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'name' => 'name',
            'department' => 'department',
            'organization'=>'organization',
            'lang' => 'lang',
            'isActive'=>'isActive',
                'processId'=>'processId'
        ];
    }
}
