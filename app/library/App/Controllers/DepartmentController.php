<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
// use App\Model\Group;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\SystemicMap;
use App\Model\SystemicMapItems;
use App\Model\SystemicMapChain;
use App\Model\ActionListGroup;
use App\Model\Department;
use Phalcon\Http\Request;
use App\Constants\AclRoles;
use Phalcon\Mvc\Model\Query;
use App\Model\Group;


class DepartmentController extends CrudResourceController
{
    public function createDepartment()
    {
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }

        $creator = $this->getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;
        $request = new Request();
        $data = $request->getJsonRawBody();
        $department = new \App\Model\Department();
        $department->title = $data->title;
        $department->description = $data->description;
        $department->organization_id = $organization;
        if ($department->save() == false) {
            $messagesErrors = array();
            foreach ($department->getMessages() as $message) {
                // print_r($message);
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $departmentId = $department->getWriteConnection()->lastInsertId();
            $response = [
              'code' => 1,
              'status' => 'Success',
              'data' => array('departmentId' => $departmentId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }


    public function getDepartment(){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }

        $creator = $this->getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;
        $departments = Department::find(
        [
            'conditions' => '	organization_id = ?1',
            'bind' => [
                1 => $organization,
            ],
        ]
      );


      $response = [
    'code' => 1,
    'status' => 'Success',
    'data' => $departments,
  ];

      return $this->createArrayResponse($response, 'data');
    }


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
}
