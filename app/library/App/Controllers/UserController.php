<?php

namespace App\Controllers;

use App\Model\ProcessUsers;
use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\User;
use App\Model\Organization;
use Phalcon\Http\Request;
use App\Model\ProcessDepartments;

class UserController extends CrudResourceController
{
    public function me()
    {
        return $this->createResourceResponse($this->userService->getDetails());
    }

    public function refreshToken(){
      if ($this->authManager->loggedIn()) {
        $response = [
            'status' => 0,
            'msg' => "you are logged in"
        ];
      }else{
        $response = [
            'status' => 1,
            'msg' => "you are not logged in"
        ];
      }

      return $this->createArrayResponse($response, 'data');
    }


    public function authenticate()
    {
        $username = $this->request->getUsername();
        $password = $this->request->getPassword();

        $session = $this->authManager->loginWithUsernamePassword(\App\Auth\UsernameAccountType::NAME, $username,
            $password);

        $transformer = new \App\Transformers\UserTransformer();
        $transformer->setModelClass('App\Model\User');

        $user = $this->createItemResponse(\App\Model\User::findFirst($session->getIdentity()), $transformer);

        $response = [
            'token' => $session->getToken(),
            'expires' => $session->getExpirationTime(),
            'user' => $user,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function createManager()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $adminId = $session->getIdentity(); // For example; 1
        // $user = \Users::findFirstById($userId);
        }

        $organizations = Organization::find(
        [
            'conditions' => 'userId = ?1',
            'bind' => [
                1 => $adminId,
            ],
        ]
      );
        $orgs = array();
        if ($organizations) {
            foreach ($organizations as $or) {
                $organization_id = $or->id;
            }
      //  echo $organization_id;die();
        } else {
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => "Admin's organization not found!",
        ];

            return $this->createArrayResponse($response, 'data');
        }

      // error_reporting(E_ERROR | E_PARSE);

      $request = new Request();
        $data = $request->getJsonRawBody();

      // var_dump($data);die();
      //check for required fields
      $validate = array(
        'password' => array('mandatory' => true, 'regex' => null),
        'email' => array('mandatory' => true, 'regex' => null),
      );

        $missing_input = array();

        foreach ($data as $key => $val) {
            $mandatory = isset($validate[$key]) ? $validate[$key] : false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
            'code' => 0,
            'status' => 'Required field: '.implode(', ', $missing_input),
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //check for duplicates
      $user = User::findFirst(
          [
              'conditions' => 'email = ?1 OR username = ?2',
              'bind' => [
                  1 => $data->email,
                  2 => $data->username,
              ],
          ]
      );
        if ($user) {
            if ($user->email == $data->email) {
                $errorText = 'Email';
            }
            if ($user->username == $data->username) {
                $errorText = 'Username';
            }
            $response = [
            'code' => 0,
            'status' => $errorText.' exists!',
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //create new manager user
      $manager = new \App\Model\User();
        $manager->role = \App\Constants\AclRoles::MANAGER;
        $manager->email = $data->email;
        $manager->username = $data->username;
        $manager->password = $this->security->hash($data->password);
        $manager->firstName = $data->firstName;
        $manager->lastName = $data->LastName;
        $manager->createdAt = '2017-07-06 02:25:00';

        if ($manager->save() == false) {
            $messagesErrors = array();
            foreach ($manager->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => $messagesErrors,
        ];
        } else {
        //  echo "aaaa";die();
            $managerId = $manager->getWriteConnection()->lastInsertId();
            $assign_org = new \App\Model\UserOrganization();
            $assign_org->organization_id = $organization_id;
            $assign_org->user_id = $managerId;
            if ($assign_org->save() == false) {
                $messagesErrors = array();
                foreach ($assign_org->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
                $response = [
              'code' => 0,
              'status' => 'Error2',
              'data' => $messagesErrors,
          ];
            } else {
                $response = [
                       'code' => 1,
                       'status' => 'Success',
                       'data' => array(
                         'userid' => $managerId,
                       ),
                   ];
            }
        }

      //response
      return $this->createArrayResponse($response, 'data');
    }

    public function createUser()
    {
        $request = new Request();
        $data = $request->getJsonRawBody();

      //check for required fields
      $validate = array(
        'password' => array('mandatory' => true, 'regex' => null),
        'email' => array('mandatory' => true, 'regex' => null),
      );

        $missing_input = array();

        foreach ($data as $key => $val) {
            $mandatory = isset($validate[$key]) ? $validate[$key] : false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
            'code' => 0,
            'status' => 'Required field: '.implode(', ', $missing_input),
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //check for duplicates
      $user = User::findFirst(
          [
              'conditions' => 'email = ?1 OR username = ?2',
              'bind' => [
                  1 => $data->email,
                  2 => $data->username,
              ],
          ]
      );
        if ($user) {
            if ($user->email == $data->email) {
                $errorText = 'Email';
            }
            if ($user->username == $data->username) {
                $errorText = 'Username';
            }
            $response = [
            'code' => 0,
            'status' => $errorText.' exists!',
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //create new manager user
      $manager = new \App\Model\User();
        $manager->role = \App\Constants\AclRoles::USER;
        $manager->email = $data->email;
        $manager->username = $data->username;
        $manager->password = $this->security->hash($data->password);
        $manager->firstName = $data->firstName;
        $manager->lastName = $data->LastName;
        $manager->createdAt = '2017-07-06 02:25:00';

        if ($manager->save() == false) {
            $messagesErrors = array();
            foreach ($manager->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => $messagesErrors,
        ];
        } else {
            $managerId = $manager->getWriteConnection()->lastInsertId();
            $response = [
             'code' => 1,
             'status' => 'Success',
             'data' => array(
               'userid' => $managerId,
             ),
         ];
        }

      //response
      return $this->createArrayResponse($response, 'data');
    }


    public function whitelist()
    {
        return [
            'firstName',
            'lastName',
            'password',
        ];
    }

    public function createManagerPublic()
    {




      // error_reporting(E_ERROR | E_PARSE);

      $request = new Request();
        $data = $request->getJsonRawBody();
$organization_id = $data->organization;
      // var_dump($data);die();
      //check for required fields
      $validate = array(
        'password' => array('mandatory' => true, 'regex' => null),
        'email' => array('mandatory' => true, 'regex' => null),
      );

        $missing_input = array();

        foreach ($data as $key => $val) {
            $mandatory = isset($validate[$key]) ? $validate[$key] : false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
            'code' => 0,
            'status' => 'Required field: '.implode(', ', $missing_input),
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //check for duplicates
      $user = User::findFirst(
          [
              'conditions' => 'email = ?1 OR username = ?2',
              'bind' => [
                  1 => $data->email,
                  2 => $data->username,
              ],
          ]
      );
        if ($user) {
            if ($user->email == $data->email) {
                $errorText = 'Email';
            }
            if ($user->username == $data->username) {
                $errorText = 'Username';
            }
            $response = [
            'code' => 0,
            'status' => $errorText.' exists!',
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //create new manager user
      $manager = new \App\Model\User();
        $manager->role = \App\Constants\AclRoles::MANAGER;
        $manager->email = $data->email;
        $manager->username = $data->username;
        $manager->password = $this->security->hash($data->password);
        $manager->firstName = $data->firstName;
        $manager->lastName = $data->LastName;
        $manager->createdAt = '2017-07-06 02:25:00';

        if ($manager->save() == false) {
            $messagesErrors = array();
            foreach ($manager->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => $messagesErrors,
        ];
        } else {
        //  echo "aaaa";die();
            $managerId = $manager->getWriteConnection()->lastInsertId();
            $assign_org = new \App\Model\UserOrganization();
            $assign_org->organization_id = $organization_id;
            $assign_org->user_id = $managerId;
            if ($assign_org->save() == false) {
                $messagesErrors = array();
                foreach ($assign_org->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
                $response = [
              'code' => 0,
              'status' => 'Error2',
              'data' => $messagesErrors,
          ];
            } else {
                $response = [
                       'code' => 1,
                       'status' => 'Success',
                       'data' => array(
                         'userid' => $managerId,
                       ),
                   ];
            }
        }

      //response
      return $this->createArrayResponse($response, 'data');
    }

    public function createUserPublic()
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        $organization_id = $data->organization;
      //check for required fields
      $validate = array(
        'password' => array('mandatory' => true, 'regex' => null),
        'email' => array('mandatory' => true, 'regex' => null),
      );

        $missing_input = array();

        foreach ($data as $key => $val) {
            $mandatory = isset($validate[$key]) ? $validate[$key] : false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
            'code' => 0,
            'status' => 'Required field: '.implode(', ', $missing_input),
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //check for duplicates
      $user = User::findFirst(
          [
              'conditions' => 'email = ?1 OR username = ?2',
              'bind' => [
                  1 => $data->email,
                  2 => $data->username,
              ],
          ]
      );
        if ($user) {
            if ($user->email == $data->email) {
                $errorText = 'Email';
            }
            if ($user->username == $data->username) {
                $errorText = 'Username';
            }
            $response = [
            'code' => 0,
            'status' => $errorText.' exists!',
        ];

            return $this->createArrayResponse($response, 'data');
        }

      //create new manager user
      $manager = new \App\Model\User();
        $manager->role = \App\Constants\AclRoles::USER;
        $manager->email = $data->email;
        $manager->username = $data->username;
        $manager->password = $this->security->hash($data->password);
        $manager->firstName = $data->firstName;
        $manager->lastName = $data->LastName;
        $manager->createdAt = '2017-07-06 02:25:00';

        if ($manager->save() == false) {
            $messagesErrors = array();
            foreach ($manager->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => $messagesErrors,
        ];
        } else {

          $managerId = $manager->getWriteConnection()->lastInsertId();
          $assign_org = new \App\Model\UserOrganization();
          $assign_org->organization_id = $organization_id;
          $assign_org->user_id = $managerId;
          if ($assign_org->save() == false) {
              $messagesErrors = array();
              foreach ($assign_org->getMessages() as $message) {
                  $messagesErrors[] = $message;
              }
              $response = [
            'code' => 0,
            'status' => 'Error2',
            'data' => $messagesErrors,
        ];
          } else {
              $response = [
                     'code' => 1,
                     'status' => 'Success',
                     'data' => array(
                       'userid' => $managerId,
                     ),
                 ];
          }
        }

      //response
      return $this->createArrayResponse($response, 'data');
    }

    public function setProcessPermissions()
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        $processId = $data->processId;

        $connection = $this->db;
        $sqlDepartment = 'DELETE FROM `process_departments` WHERE `processId` = '.$processId;
        $connection->query($sqlDepartment);

        $sqlUsers = 'DELETE FROM `process_users` WHERE `processId` = '.$processId;
        $connection->query($sqlUsers);

        $sqlOrg = 'DELETE FROM `process_organizations` WHERE `processId` = '.$processId;
        $connection->query($sqlOrg);

        //organization
        foreach ($data->organization as $valueOrganization){
            $processOrganizations = new \App\Model\ProcessOrganizations();
            $processOrganizations->processId = $processId;
            $processOrganizations->organizationId = $valueOrganization;
            $processOrganizations->save();
        }

        //departments
        foreach ($data->department as $valueDepartment){
            $processDepartments = new ProcessDepartments();
            $processDepartments->processId = $processId;
            $processDepartments->departmentId = $valueDepartment;
            $processDepartments->save();
        }
        //persons
        foreach ($data->persons as $valuePersons){
            $processUsers = new \App\Model\ProcessUsers();
            $processUsers->processId = $processId;
            $processUsers->userId = $valuePersons;
            $processUsers->save();
        }

        $response = [
            'code' => 1,
            'status' => 'Success'
        ];

        return $this->createResponse($response);
    }


    public function updateUser(){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $request = new Request();
        $data = $request->getJsonRawBody();


        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $creatorId
                ],
            ]);

        if($user) {
            $user->firstName = $data->firstName;
            $user->lastName = $data->lastName;
            $user->location = $data->location;
            if ($user->save() == false) {
                $messagesErrors = array();
                foreach ($user->getMessages() as $message) {
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
        }else{
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "User not found",
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function updateOtherUser($userId){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $request = new Request();
        $data = $request->getJsonRawBody();


        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId
                ],
            ]);

        if($user) {
            $user->firstName = $data->firstName;
            $user->lastName = $data->lastName;
            $user->location = $data->location;
            if ($user->save() == false) {
                $messagesErrors = array();
                foreach ($user->getMessages() as $message) {
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
        }else{
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "User not found",
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function deactivateOtherUser($userId){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $request = new Request();
        $data = $request->getJsonRawBody();


        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId
                ],
            ]);

        if($user) {
            $user->firstName = "deleted";
            $user->lastName = "deleted";
            $user->email = "deleted@deleted.com";
            $user->location = $data->location;
            if ($user->save() == false) {
                $messagesErrors = array();
                foreach ($user->getMessages() as $message) {
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
        }else{
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "User not found",
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

    public function getProcessPermissions($permissionId){

        $permissions = array();
//        $permissions['departments'] = $processDepartments;
//        $permissions['users'] = $processUsers;
        $processDepartments =  ProcessDepartments::find(
            [
                'conditions' => 'processId = ?1',
                'bind' => [
                    1 =>$permissionId
                ],
            ]
        );


        $processUsers =  ProcessUsers::find(
            [
                'conditions' => 'processId = ?1',
                'bind' => [
                    1 =>$permissionId
                ],
            ]
        );

        foreach ($processDepartments as $dep) {
            $permissions['departments'][] = (int) $dep->departmentId;
        }



        foreach ($processUsers as $depU) {
            $permissions['users'][] = (int) $depU->userId;
        }

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $permissions
        ];
        return $this->createArrayResponse($response, 'data');
    }

    public function getUsers(){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        if ($creator['organization'] == null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        $connection = $this->db;
        $sql_dist = 'SELECT U.* FROM user U INNER JOIN user_organizationHide O ON O.user_id = U.id WHERE O.organization_id = '.$organization_id.' AND U.email != "deleted@deleted.com" ';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();

//        $a = array(
//            'tree' => $tree,
//        );

        return $this->createArrayResponse($results_dist, 'users');



    }
}
