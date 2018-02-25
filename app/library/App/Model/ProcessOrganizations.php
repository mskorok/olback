<?php

namespace App\Model;

class ProcessOrganizations  extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $processId;
    public $organizationId;

    public function getSource()
    {
        return 'process_organizations';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'processId' => 'processId',
                'organizationId' => 'organizationId'
            ];
    }
}
