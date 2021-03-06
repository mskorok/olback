<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class SystemicStructureMapItemsOld extends DateTrackingModel
{
    public $id;
    public $systemic_map_id;
    public $question;
    public $proposal;
    public $groupId;
    public $userId;
    public $itemType;

    public function getSource()
    {
        return 'systemic_map_structure_items';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'systemic_map_id' => 'systemic_map_id',
            'question' => 'question',
            'proposal'=>'proposal',
            'groupId'=>'groupId',
            'userId'=>'userId',
                'itemType'=>'itemType'
        ];
    }
}
