<?php

namespace App\Auth;

use App\Constants\Services;
use App\Model\User;
use Phalcon\Di;
use PhalconApi\Auth\AccountType;

class EmailAccountType implements AccountType
{
    const NAME = 'username';

    /**
     * @param array $data
     * @return null|string
     */
    public function login($data)
    {
        /** @var \Phalcon\Security $security */
        $security = Di::getDefault()->get(Services::SECURITY);

        $email = $data[Manager::LOGIN_DATA_EMAIL];
        $password = $data[Manager::LOGIN_DATA_PASSWORD];

        /** @var User $user */
        $user = User::findFirst([
            'conditions' => 'email = :email:',
            'bind' => ['email' => $email]
        ]);

        if (!$user) {
            return null;
        }

        if (!$security->checkHash($password, $user->password)) {
            return null;
        }

        return (string)$user->id;
    }

    /**
     * @param string $identity
     * @return bool
     */
    public function authenticate($identity): bool
    {
        return User::count([
            'conditions' => 'id = :id:',
            'bind' => ['id' => (int)$identity]
        ]) > 0;
    }
}
