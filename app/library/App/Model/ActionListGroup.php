<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class ActionListGroup extends DateTrackingModel
{
    public $id;
    public $systemic_map_id;
    public $title;
    public $description;
    public $created_by;

    public function getSource()
    {
        return 'action_list_group';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'systemic_map_id' => 'systemic_map_id',
            'title' => 'title',
            'description'=>'description',
            'created_by'=>'created_by'
        ];
    }
}
