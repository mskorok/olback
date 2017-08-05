<?php

namespace App\Model;

class SystemicMapChain extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $from_item;
    public $to_item;

    public function getSource()
    {
        return 'systemic_map_chain';
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
