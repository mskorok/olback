<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Constants\Services;
use PhalconRest\Export\Documentation;
use PhalconRest\Export\Postman\ApiCollection;
use PhalconRest\Mvc\Controllers\CollectionController;
use PhalconRest\Transformers\DocumentationTransformer;
use PhalconRest\Transformers\Postman\ApiCollectionTransformer;


class UserController extends CrudResourceController
{
    public function me()
    {
        return $this->createResourceResponse($this->userService->getDetails());
    }

    public function authenticate()
    {
        $username = $this->request->getUsername();
        $password = $this->request->getPassword();

        $session = $this->authManager->loginWithUsernamePassword(\App\Auth\UsernameAccountType::NAME, $username,
            $password);

        $transformer = new \App\Transformers\UserTransformer;
        $transformer->setModelClass('App\Model\User');

        $user = $this->createItemResponse(\App\Model\User::findFirst($session->getIdentity()), $transformer);

        $response = [
            'token' => $session->getToken(),
            'expires' => $session->getExpirationTime(),
            'user' => $user
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function createManager(){
      // echo 'aaaa';die();
      // $session = $this->authM
      // use PhalconRest\Http\Request;anager->getSession();
      //
      // $response = [
      //     'token' => $session->getToken(),
      //     'expires' => $session->getExpirationTime()
      // ];

      // $authManager = $this->di->get(AppServices::AUTH_MANAGER);
      // if ($authManager->loggedIn()) {
      //
      //     echo 'dsds';die();
      //
      // }

      $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => array(
            'userid'=>123
          )
      ];

      return $this->createArrayResponse($response, 'data');


      $query = $this->modelsManager->createQuery('SELECT * FROM User');
      $users  = $query->execute();
      print_r($users);die();
    }



    public function whitelist()
    {
        return [
            'firstName',
            'lastName',
            'password'
        ];
    }
}
