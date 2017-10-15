<?php

namespace App\Model;

class Survey extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $title;
    public $description;
    public $isEditable;
    public $isOlset;

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
            'isOlset' => 'isOlset'
        ];
    }
}
