<?php

namespace App\Model;


class Process extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $title;
    public $CurrentReality;
    public $InitialIntentions;


    public function getSource()
    {
        return 'process';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'title' => 'title',
                'CurrentReality' => 'CurrentReality',
                'InitialIntentions' => 'InitialIntentions'
            ];
    }

    public function initialize() {
    }
}