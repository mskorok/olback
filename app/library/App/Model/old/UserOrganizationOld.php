<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class UserOrganizationOld extends DateTrackingModel
{
    public $id;
    public $organization_id;
    public $user_id;

    public function getSource()
    {
        return 'user_organization';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'user_id' => 'user_id',
            'organization_id' => 'organization_id'
        ];
    }
}
