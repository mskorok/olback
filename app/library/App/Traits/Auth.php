<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 02.08.18
 * Time: 16:11
 */

namespace App\Traits;

use App\Model\User;
use App\Model\UserOrganization;
use Phalcon\Mvc\Model;

trait Auth
{
    /**
     * @return null|string
     *
     */
    public function getAuthenticatedId()
    {
        $userId = null;
        /** @var \PhalconApi\Auth\Manager $manager */
        $manager = $this->authManager;
        if ($manager->loggedIn()) {
            $session = $manager->getSession();
            return $session ? (int) $session->getIdentity() : null;
        }
        return null;
    }

    /**
     * @return null|Model
     *
     */
    public function getAuthenticated()
    {
        $userId = null;
        /** @var \PhalconApi\Auth\Manager $manager */
        $manager = $this->authManager;
        if ($manager->loggedIn()) {
            $session = $manager->getSession();
            $userId = $session ? $session->getIdentity() : null;
            return User::findFirst($userId);
        }
        return null;
    }

    /**
     * @param $userId
     * @return array|null
     */
    public static function getUserDetails($userId)
    {
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );
        if ($user instanceof User) {
            $organization = UserOrganization::findFirst(
                [
                    'conditions' => 'user_id = ?1',
                    'bind' => [
                        1 => $userId,
                    ],
                ]
            );

            if ($organization instanceof UserOrganization) {
                return ['account' => $user, 'organization' => $organization];
            }
            return ['account' => $user, 'organization' => null];
        }
        return null;
    }
}
