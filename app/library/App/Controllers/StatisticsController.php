<?php

namespace App\Controllers;

use App\Constants\Services;
use PhalconRest\Export\Documentation;
use PhalconRest\Export\Postman\ApiCollection;
use PhalconRest\Mvc\Controllers\CollectionController;
use PhalconRest\Transformers\DocumentationTransformer;
use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\UserOrganization;
use App\Model\User;
class StatisticsController extends CollectionController
{

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
        if ($user) {
            $organization = UserOrganization::findFirst(
                [
                    'conditions' => 'user_id = ?1',
                    'bind' => [
                        1 => $userId,
                    ],
                ]
            );

            if ($organization) {
                return array('account' => $user, 'organization' => $organization);
            } else {
                return array('account' => $user, 'organization' => null);
            }
        } else {
            return null;
        }
    }

    public function getDashboardStats()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;
        $connection = $this->db;
        $sql_dist = 'SELECT COUNT(U.id) AS count,role FROM `user` U INNER JOIN user_organization UO ON U.id = UO.user_id WHERE (U.role = \'Manager\' OR U.role = \'User\' ) AND UO.organization_id = '.$organization.' GROUP BY role';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_USERS = $data_dist->fetchAll();

//SELECT count(id),CASE WHEN status = 0 THEN "stopped" ELSE "running" END FROM `process` WHERE organizationId = 1 GROUP BY status

        $sql_dist_org = 'SELECT count(id) as count,CASE WHEN status = 0 THEN "stopped" ELSE "running" END as status FROM `process` WHERE organizationId = '.$organization.' GROUP BY status';
        $data_dist_org = $connection->query($sql_dist_org);
        $data_dist_org->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_ORGS = $data_dist_org->fetchAll();





        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => array(
                'count_users'=>$COUNT_USERS,
                'count_organizations'=>$COUNT_ORGS
            ),
        ];

        return $this->createArrayResponse($response, 'data');


    }
}
