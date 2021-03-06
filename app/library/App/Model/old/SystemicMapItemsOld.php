<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class SystemicMapItemsOld extends DateTrackingModel
{
    public $id;
    public $systemic_map_id;
    public $question;
    public $proposal;
    public $groupId;
    public $userId;

    public function getSource()
    {
        return 'systemic_map_items';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'systemic_map_id' => 'systemic_map_id',
            'question' => 'question',
            'proposal'=>'proposal',
            'groupId'=>'groupId',
            'userId'=>'userId'
        ];
    }
}
