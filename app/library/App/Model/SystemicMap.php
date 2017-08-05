<?php

namespace App\Model;

class SystemicMap extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $name;
    public $department;
    public $organization;
    public $isActive;
    public $lang;

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
            'isActive'=>'isActive'
        ];
    }
}
