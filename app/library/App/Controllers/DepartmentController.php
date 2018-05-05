<?php

namespace App\Controllers;

use App\Model\ProcessDepartments;
use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
// use App\Model\Group;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\Department;
use App\Model\UserDepartment;
use Phalcon\Http\Request;

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

    public function getDepartment()
    {
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

        $deps = array();
        foreach ($departments as $d){
            $deps[] = array(
                "id"=>(int)$d->id,
                "title"=>$d->title,
                "description"=>$d->description,
                "organization_id"=>$d->organization_id
            );
        }

        $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => $deps,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function updateDepartment($id){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }
      $request = new Request();
      $data = $request->getJsonRawBody();
      $creator = $this->getUserDetails($creatorId);
      $organization = $creator['organization']->organization_id;
      if ($creator['organization'] == null) {
          $response = [
        'code' => 0,
        'status' => 'Error',
        'data' => "Manager's organization not found!",
      ];

          return $this->createArrayResponse($response, 'data');
      }


          $department = Department::findFirst(
      [
          'conditions' => 'id = ?1 AND organization_id = ?2',
          'bind' => [
              1 => $id,
              2 => $organization,
          ],
      ]);

      if ($department->id) {
      //  echo $department->id;die();
          if (isset($data->title)) {
              $department->title = $data->title;
          }
          if (isset($data->description)) {
              $department->description = $data->description;
          }
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
            $response = [
                'code' => 1,
                'status' => 'Success'
            ];
          }
      } else {
          $response = [
            'code' => 0,
            'status' => 'You cannot edit this department!',
          ];
      }

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

    public function assignUserDepartment($userId){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $request = new Request();
        $data = $request->getJsonRawBody();

        //check for user
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );
        if ($user) {
            foreach($data->departments as $departmentId){
                $department = new UserDepartment();
                $department->user_id = $userId;
                $department->department_id = $departmentId;
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
                    return $this->createArrayResponse($response, 'data');
                }
            }

            $response = [
                'code' => 1,
                'status' => 'Success'
            ];
        }else{
            $response = [
                'code' => 0,
                'status' => 'User does not exists'
            ];
        }


        return $this->createArrayResponse($response, 'data');
    }


    public function deleteDepartment($departmentId){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;

        //check if user is authorized to delete the department
        $department = Department::findFirst(
            [
                'conditions' => 'id = ?1 AND organization_id = ?2',
                'bind' => [
                    1 => $departmentId,
                    2 => $organization,
                ],
            ]);

        if($department){
            $userDepartment = UserDepartment::findFirst(
                [
                    'conditions' => 'department_id = ?1',
                    'bind' => [
                        1 => $departmentId,
                    ],
                ]);
                if($userDepartment){
                    $response = [
                        'code' => 0,
                        'status' => 'Assigned users'
                    ];
                    return $this->createArrayResponse($response, 'data');
                }

            $processDepartment = ProcessDepartments::findFirst(
                [
                    'conditions' => 'departmentId = ?1',
                    'bind' => [
                        1 => $departmentId,
                    ],
                ]);
            if($processDepartment){
                $response = [
                    'code' => 0,
                    'status' => 'Assigned processes'
                ];
                return $this->createArrayResponse($response, 'data');
            }

            $department->delete();
            $response = [
                'code' => 1,
                'status' => 'Success',
            ];


        }else{
            $response = [
                'code' => 0,
                'status' => 'You are not authorized to delete the department'
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }


    public function getUserDepartments($userId){

        $userDepartments = UserDepartment::find(
            [
                'conditions' => 'user_id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]);

        $departmentIds = array();
        foreach ($userDepartments as $d){
            $departmentIds[] = (int)$d->department_id;
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $departmentIds
        ];
        return $this->createArrayResponse($response, 'data');
    }


    public function updateUserDepartments($userId){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $request = new Request();
        $data = $request->getJsonRawBody();

        //check for user
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );
        if ($user) {
            $userDepartments = UserDepartment::find(
                [
                    'conditions' => 'user_id = ?1',
                    'bind' => [
                        1 => $userId,
                    ],
                ]);


            foreach ($userDepartments as $d){
                $d->delete();
            }

            foreach($data->departments as $departmentId){
                $department = new UserDepartment();
                $department->user_id = $userId;
                $department->department_id = $departmentId;
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
                    return $this->createArrayResponse($response, 'data');
                }
            }

            $response = [
                'code' => 1,
                'status' => 'Success'
            ];
        }else{
            $response = [
                'code' => 0,
                'status' => 'You are not authorized to update departments'
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }
}
