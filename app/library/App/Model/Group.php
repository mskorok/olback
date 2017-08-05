<?php

namespace App\Model;

class Group extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $title;
    public $organization;
    public $creatorId;

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
            'creatorId'=>'creatorId'
        ];
    }
}
