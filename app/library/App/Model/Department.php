<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class Department extends DateTrackingModel
{
    public $id;
    public $title;
    public $description;
    public $organization_id;

    public function getSource()
    {
        return 'departments';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'organization_id'=>'organization_id',
        ];
    }
}
