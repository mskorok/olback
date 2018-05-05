<?php

namespace App\Model;

class UserDepartment extends \App\Mvc\DateTrackingModel
{
    public $id;
    public $department_id;
    public $user_id;

    public function getSource()
    {
        return 'user_department';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'user_id' => 'user_id',
            'department_id' => 'department_id'
        ];
    }
}
