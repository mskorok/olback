<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\User;
use App\Model\Group;
use App\Constants\AclRoles;
use Phalcon\Http\Request;

class GroupController extends CrudResourceController
{
    public function getGroups()
    {

        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session->getIdentity(); // For example; 1
        }
        $creator = \App\Controllers\SystemicMapController::getUserDetails($userId);
        if ($creator['organization'] == null) {
            $response = [
          'code' => 0,
          'status' => 'Error',
          'data' => "Manager's organization not found!",
        ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        $groups = Group::find(
      [
          'conditions' => '	organization = ?1',
          'bind' => [
              1 => $organization_id,
          ],
      ]
    );



        $groupArray = array();
        if ($groups) {
            foreach ($groups as $group) {
                $groupArray[] = array(
        'id' => (int) $group->id,
        'title' => $group->title,
        'color' =>  $group->color,
                    'organization' => $group->organization,
                    'creatorId' => $group->creatorId,
      );
            }
        }
        $response = [
        'code' => 1,
        'status' => 'Success',
        'data' => $groupArray,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function createGroup()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session->getIdentity(); // For example; 1
        }
        $creator = \App\Controllers\SystemicMapController::getUserDetails($userId);
        if ($creator['organization'] == null) {
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => "Manager's organization not found!",
          ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;
//echo $organization_id;die();
        $request = new Request();
        $data = $request->getJsonRawBody();

        $group = new \App\Model\Group();
        $group->title = $data->title;
        $group->organization = $organization_id;
        $group->creatorId = $userId;
        $group->color = $data->color;
        if ($group->save() == false) {
            $messagesErrors = array();
            foreach ($group->getMessages() as $message) {
                print_r($message);
                $messagesErrors[] = $message;
            }
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => $messagesErrors,
        ];
        } else {
            $groupId = $group->getWriteConnection()->lastInsertId();
            $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => array('systemicMapId' => $groupId),
        ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function updateGroup($id)
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session->getIdentity(); // For example; 1
        }
        $creator = \App\Controllers\SystemicMapController::getUserDetails($userId);
        if ($creator['organization'] == null) {
            $response = [
          'code' => 0,
          'status' => 'Error',
          'data' => "Manager's organization not found!",
        ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        $user = User::findFirst(
      [
          'conditions' => 'id = ?1',
          'bind' => [
              1 => $userId,
          ],
      ]);

        if ((AclRoles::MANAGER === $user->role) || (AclRoles::ADMINISTRATOR === $user->role)) {
            $group = Group::findFirst(
        [
            'conditions' => 'id = ?1 AND organization = ?2',
            'bind' => [
                1 => $id,
                2 => $organization_id,
            ],
        ]);
        } else {
            $group = Group::findFirst(
        [
            'conditions' => 'id = ?1 AND creatorId = ?2',
            'bind' => [
                1 => $id,
                2 => $userId,
            ],
        ]);
        }
        if ($group) {
            $group->title = $data->title;
            if(isset($data->color)){
              $group->color = $data->color;
            }

            $group->save();
            $response = [
          'code' => 1,
          'status' => 'Success',
        ];
        } else {
            $response = [
          'code' => 0,
          'status' => 'You cannot edit this group!',
        ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function deleteGroup($id)
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session->getIdentity(); // For example; 1
        }
        $creator = \App\Controllers\SystemicMapController::getUserDetails($userId);
        if ($creator['organization'] == null) {
            $response = [
          'code' => 0,
          'status' => 'Error',
          'data' => "Manager's organization not found!",
        ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        $user = User::findFirst(
      [
          'conditions' => 'id = ?1',
          'bind' => [
              1 => $userId,
          ],
      ]);

        if ((AclRoles::MANAGER === $user->role) || (AclRoles::ADMINISTRATOR === $user->role)) {
            $group = Group::findFirst(
        [
            'conditions' => 'id = ?1 AND organization = ?2',
            'bind' => [
                1 => $id,
                2 => $organization_id,
            ],
        ]);
        } else {
            $group = Group::findFirst(
        [
            'conditions' => 'id = ?1 AND creatorId = ?2',
            'bind' => [
                1 => $id,
                2 => $userId,
            ],
        ]);
        }
        if ($group) {
            $group->delete();
            $response = [
          'code' => 1,
          'status' => 'Success',
        ];
        } else {
            $response = [
          'code' => 0,
          'status' => 'You cannot delete this group!',
        ];
        }

        return $this->createArrayResponse($response, 'data');
    }
}
