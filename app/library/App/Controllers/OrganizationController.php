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

    print_r($orgs);die();
  }



  public function createOrg(){
    $request = new Request();
    $data = $request->getJsonRawBody();



    if ($this->authManager->loggedIn()) {
      $session = $this->authManager->getSession();
      $userId = $session->getIdentity(); // For example; 1
      // $user = \Users::findFirstById($userId);
    }


// var_dump($data->name);die();
// echo "Dfsds";die();


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
}
