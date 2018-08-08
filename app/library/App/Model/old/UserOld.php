<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class UserOld extends DateTrackingModel
{
    public $id;
    public $role;
    public $email;
    public $location;
    public $firstName;
    public $lastName;
    public $username;
    public $password;

    public function getSource()
    {
        return 'user';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'role' => 'role',
            'email' => 'email',
            'username' => 'username',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'location' => 'location',
            'password' => 'password'
        ];
    }
}
