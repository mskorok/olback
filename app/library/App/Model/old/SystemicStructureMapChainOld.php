<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class SystemicStructureMapChainOld extends DateTrackingModel
{
    public $id;
    public $from_item;
    public $to_item;

    public function getSource()
    {
        return 'systemic_structure_map_chain';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'from_item' => 'from_item',
            'to_item' => 'to_item'
        ];
    }
}
