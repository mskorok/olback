<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\User;
use App\Model\Organization;
use Phalcon\Http\Request;

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
                         'userid' => 123,
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
            $response = [
             'code' => 1,
             'status' => 'Success',
             'data' => array(
               'userid' => 123,
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
}
