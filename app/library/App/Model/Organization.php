<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class Organization extends DateTrackingModel
{
    public $id;
    public $name;
    public $description;
    public $userId;

    public function getSource()
    {
        return 'organization';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'name' => 'name',
            'description' => 'description',
            'userId'=>'userId'
        ];
    }
}
