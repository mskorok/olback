<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class ProcessOrganizations extends DateTrackingModel
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
