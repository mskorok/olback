<?php

namespace App\Model;


class Process extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $title;
    public $CurrentReality;
    public $InitialIntentions;
    public $step0;
    public $step3_0;
    public $step3_1;
    public $status;
    public $organizationId;

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
                'InitialIntentions' => 'InitialIntentions',
                'step0'=>'step0',
                'step3_0'=>'step3_0',
                'step3_1'=>'step3_1',
                'status'=>'status',
                'organizationId'=>'organizationId'
            ];
    }

    public function initialize() {
    }
}