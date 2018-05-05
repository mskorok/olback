<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Constants\Services;
use PhalconRest\Export\Documentation;
use PhalconRest\Export\Postman\ApiCollection;
use PhalconRest\Mvc\Controllers\CollectionController;
use PhalconRest\Transformers\DocumentationTransformer;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use Phalcon\Di;
use PhalconApi\Auth\Session;
use PhalconRest\Exception;
use App\Model\Organization;

use Phalcon\Http\Request;




class OrganizationController extends CrudResourceController
{


  public function getOrgs(){
    if ($this->authManager->loggedIn()) {
      $session = $this->authManager->getSession();
      $userId = $session->getIdentity(); // For example; 1
      // $user = \Users::findFirstById($userId);
    }



    $organizations = Organization::find(
      [
          'conditions' => 'userId = ?1',
          'bind'       => [
              1 => $userId
          ]
      ]
    );
    $orgs = array();
    if($organizations){
      foreach($organizations as $or){
        $orgs[] = array(
          'id'=>$or->id,
          'name'=>$or->name,
          'description'=>$or->description
        );
      }
    }
      $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => $orgs
      ];

      return $this->createArrayResponse($response, 'data');
  }



  public function createOrg(){
    $request = new Request();
    $data = $request->getJsonRawBody();



    if ($this->authManager->loggedIn()) {
      $session = $this->authManager->getSession();
      $userId = $session->getIdentity(); // For example; 1
      // $user = \Users::findFirstById($userId);
    }

      $organizationCheck = Organization::findFirst(
          [
              'conditions' => 'userId = ?1',
              'bind'       => [
                  1 => $userId
              ]
          ]
      );

      if($organizationCheck) {
          $response = [
              'code' => 1,
              'status' => 'Cannot create organization'
          ];

          return $this->createArrayResponse($response, 'data');
      }


    //
    $organization = new \App\Model\Organization();
    $organization->name = $data->name;
    $organization->description = $data->description;
    $organization->userId = $userId;
    if ($organization->save() == false) {
      $messagesErrors = array();
      foreach ($organization->getMessages() as $message) {
        $messagesErrors[]=$message;
      }
      $response = [
          'code' => 0,
          'status' => 'Error',
          'data' => $messagesErrors
      ];
     } else {
       $response = [
           'code' => 1,
           'status' => 'Success'
       ];
     };
     return $this->createArrayResponse($response, 'data');

  }

  public function updateOrg(){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $userId = $session->getIdentity(); // For example; 1
          // $user = \Users::findFirstById($userId);
      }

      $request = new Request();
      $data = $request->getJsonRawBody();

      $organization = Organization::findFirst(
          [
              'conditions' => 'userId = ?1',
              'bind'       => [
                  1 => $userId
              ]
          ]
      );

      if($organization) {
          if (isset($data->name))
              $organization->name = $data->name;
          if (isset($data->description))
              $organization->description = $data->description;
          if ($organization->save() == false) {
              $messagesErrors = array();
              foreach ($organization->getMessages() as $message) {
                  $messagesErrors[] = $message;
              }
              $response = [
                  'code' => 0,
                  'status' => 'Error',
                  'data' => $messagesErrors
              ];
          } else {
              $response = [
                  'code' => 1,
                  'status' => 'Success'
              ];
          };
      }else{
          $response = [
              'code' => 0,
              'status' => 'Organization does not exist'
          ];
      }
      return $this->createArrayResponse($response, 'data');

  }
}
