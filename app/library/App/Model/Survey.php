<?php

namespace App\Model;

class Survey extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $title;
    public $description;
    public $isEditable;
    public $isOlset;
    public $creator;
    public $organization_id;

    public function getSource()
    {
        return 'survey';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'isEditable'=>'isEditable',
            'isOlset' => 'isOlset',
            'creator' => 'creator',
            'organization_id' => 'organization_id'
        ];
    }
}
