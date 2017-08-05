<?php

namespace App\Controllers;

use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\SystemicMap;
use App\Model\SystemicMapItems;
use App\Model\SystemicMapChain;
use Phalcon\Http\Request;

class SystemicMapController extends CrudResourceController
{
    public function getSystemicMap()
    {
        //  echo 'asas';die();
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

        $systemicMaps = SystemicMap::find(
        [
            'conditions' => '	organization = ?1',
            'bind' => [
                1 => $organization_id,
            ],
        ]
      );
        $systemicMapsArray = array();
        if ($systemicMaps) {
            foreach ($systemicMaps as $systemicMap) {
                $systemicMapsArray[] = array(
            'id' => $systemicMap->id,
            'name' => $systemicMap->name,
            'department' => $systemicMap->department,
            'organization' => $systemicMap->organization,
            'isActive' => $systemicMap->isActive,
          );
            }
        }
        $response = [
        'code' => 1,
        'status' => 'Success',
        'data' => $systemicMapsArray,
      ];

        return $this->createArrayResponse($response, 'data');
    }

    public function getSystemicItem($id){
//echo $id;die();
      $systemicMaps = SystemicMapItems::find(
      [
          'conditions' => '	systemic_map_id = ?1',
          'bind' => [
              1 => $id,
          ],
      ]
    );
      $systemicMapsArray = array();
      $linksArray = array();
      if ($systemicMaps) {
          foreach ($systemicMaps as $systemicMap) {
              $systemicMapsArray[] = array(
                'id' => $systemicMap->id,
                'systemic_map_id' => $systemicMap->systemic_map_id,
                'question' => $systemicMap->question,
                'proposal' => $systemicMap->proposal,
                'groupId' => $systemicMap->groupId,
              );

              $chains = SystemicMapChain::find(
              [
                  'conditions' => 'to_item =?1',
                  'bind' => [
                      1 => $systemicMap->id,
                  ],
              ]
            );
            // echo "dada";die();
            foreach ($chains as $chain) {
              $linksArray[]=array(
                'source'=>$chain->from_item,
                'target'=>$chain->to_item,
                'value2'=>2
              );
            }
          }
      }
      $response = [
      'code' => 1,
      'status' => 'Success',
      'data' => array("nodes" =>$systemicMapsArray,"links"=>$linksArray),
    ];

      return $this->createArrayResponse($response, 'data');

    }






    public function createSystemicMap()
    {
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

        $request = new Request();
        $data = $request->getJsonRawBody();

        //check for required fields
        $validate = array(
          'name' => array('mandatory' => true, 'regex' => null),
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
        $systemicMap = new \App\Model\SystemicMap();
        $systemicMap->name = $data->name;
        if ($data->department == '') {
            $systemicMap->department = null;
        } else {
            $systemicMap->department = $data->department;
        }

        $systemicMap->organization = $organization_id;
        $systemicMap->lang = $data->lang;
        $systemicMap->isActive = $data->isActive;
        if ($systemicMap->save() == false) {
            $messagesErrors = array();
            foreach ($systemicMap->getMessages() as $message) {
                print_r($message);
                $messagesErrors[] = $message;
            }
            die();
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $systemicMapId = $systemicMap->getWriteConnection()->lastInsertId();
            $response = [
              'code' => 1,
              'status' => 'Success',
              'data' => array('systemicMapId' => $systemicMapId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function createSystemicMapItem()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $request = new Request();
        $data = $request->getJsonRawBody();

        $validate = array(
        'systemic_map_id' => array('mandatory' => true, 'regex' => null),
        'question' => array('mandatory' => true, 'regex' => null),
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

        $systemicItem = new \App\Model\SystemicMapItems();
        $systemicItem->systemic_map_id = $data->systemic_map_id;
        $systemicItem->question = $data->question;
        $systemicItem->proposal = $data->proposal;
        $systemicItem->groupId = 0;
        $systemicItem->userId = $creatorId;
        if ($systemicItem->save() == false) {
            $messagesErrors = array();
            foreach ($systemicItem->getMessages() as $message) {
                //  print_r($message);
                $messagesErrors[] = $message;
            }
            //die();
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => $messagesErrors,
        ];
        } else {
            $systemicMapItemId = $systemicItem->getWriteConnection()->lastInsertId();
            $chain = new \App\Model\SystemicMapChain();
            if ($data->from_item == '') {
                $chain->from_item = null;
            } else {
                $chain->from_item = $data->from_item;
            }
            $chain->to_item = $systemicMapItemId;
            if ($chain->save() == false) {
                $messagesErrors = array();
                foreach ($chain->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
          //die();
          $response = [
          'code' => 0,
          'status' => 'Error',
          'data' => $messagesErrors,
          ];

                return $this->createArrayResponse($response, 'data');
            }

            $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => array('systemicMapItemId' => $systemicMapItemId),
        ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function getUserDetails($userId)
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