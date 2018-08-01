<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class Group extends DateTrackingModel
{
    public $id;
    public $title;
    public $organization;
    public $creatorId;
    public $color;

    public function getSource()
    {
        return 'groups';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'title' => 'title',
            'organization' => 'organization',
            'creatorId'=>'creatorId',
            'color'=>'color'
        ];
    }
}
