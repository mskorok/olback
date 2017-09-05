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
use App\Constants\AclRoles;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\ManagerInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
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

    public function getSystemicItem($id)
    {
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
                $linksArray[] = array(
                'source' => $chain->from_item,
                'target' => $chain->to_item,
                'value2' => 2,
              );
            }
            }
        }
        $response = [
      'code' => 1,
      'status' => 'Success',
      'data' => array('nodes' => $systemicMapsArray, 'links' => $linksArray),
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
                // print_r($message);
                $messagesErrors[] = $message;
            }
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
        $systemicItem->groupId = $data->groupId;
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

    public function updateSystemicMap($id)
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session->getIdentity(); // For example; 1
        }
        $creator = $this->getUserDetails($userId);
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
            $systemicMap = SystemicMap::findFirst(
        [
            'conditions' => 'id = ?1 AND organization = ?2',
            'bind' => [
                1 => $id,
                2 => $organization_id,
            ],
        ]);
        } else {
            $systemicMap = false;
        }
        if ($systemicMap) {
            if (isset($data->name)) {
                $systemicMap->name = $data->name;
            }
            if (isset($data->isActive)) {
                $systemicMap->isActive = $data->isActive;
            }
            $systemicMap->save();
            $response = [
          'code' => 1,
          'status' => 'Success',
        ];
        } else {
            $response = [
          'code' => 0,
          'status' => 'You cannot edit this systemic map!',
        ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function updateSystemicItem($id)
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
            $systemicItems = SystemicMapItems::findFirst(
        [
            'conditions' => 'id = ?1',
            'bind' => [
                1 => $id,
            ],
        ]);

            if ($systemicItems) {
                if ($userId != $systemicItems->userId) {
                    $organizationChecked = UserOrganization::findFirst(
          [
              'conditions' => 'user_id = ?1 AND organization_id = ?2',
              'bind' => [
                  1 => $systemicItems->id,
                  2 => $organization_id,
              ],
          ]);

                    if (!$organizationChecked) {
                        $response = [
                      'code' => 0,
                      'status' => 'You cannot edit this group2!',
                    ];

                        return $this->createArrayResponse($response, 'data');
                    }
                }
            }
        } else {
            $systemicItems = SystemicMapItem::findFirst(
        [
            'conditions' => 'id = ?1 AND userId = ?2',
            'bind' => [
                1 => $id,
                2 => $userId,
            ],
        ]);
        }
        if ($systemicItems) {
            if (isset($data->question)) {
                $systemicItems->question = $data->question;
            }
            if (isset($data->proposal)) {
                $systemicItems->proposal = $data->proposal;
            }
            if (isset($data->groupId)) {
                $systemicItems->groupId = $data->groupId;
            }
            $systemicItems->save();
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

    public function deleteSystemicMap($id)
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
            $systemicMap = SystemicMap::findFirst(
        [
            'conditions' => 'id = ?1',
            'bind' => [
                1 => $id,
            ],
        ]);

            if ($systemicMap) {
                $systemicChains = SystemicMapChain::find(
                [
                    'conditions' => 'from_item =?1 OR to_item =?1',
                    'bind' => [
                        1 => $systemicMap->id,
                    ],
                ]
              );
                foreach ($systemicChains as $systemicChain) {
                    $systemicChain->delete();
                }
                $systemicItems = SystemicMapItems::find(
                    [
                        'conditions' => 'systemic_map_id =?1',
                        'bind' => [
                            1 => $systemicMap->id,
                        ],
                    ]
                  );
                foreach ($systemicItems as $systemicItem) {
                    $systemicItem->delete();
                }

                $systemicMap->delete();
                $response = [
                  'code' => 1,
                  'status' => 'Success!',
                ];
            } else {
                $response = [
                'code' => 0,
                'status' => 'Systemic map not found!',
              ];
            }
        } else {
            $response = [
            'code' => 0,
            'status' => 'You cannot delete this systemic map!',
          ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function deleteSystemicItem($id)
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
            $systemicChains = SystemicMapChain::find(
                [
                    'conditions' => 'from_item =?1 OR to_item =?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
              );
            foreach ($systemicChains as $systemicChain) {
                $systemicChain->delete();
            }
            $systemicItems = SystemicMapItems::find(
                    [
                        'conditions' => 'id =?1',
                        'bind' => [
                            1 => $id,
                        ],
                    ]
                  );
            foreach ($systemicItems as $systemicItem) {
                $systemicItem->delete();
            }

            $response = [
                  'code' => 1,
                  'status' => 'Success!',
                ];
        } else {
            $systemicItems = SystemicMapItems::find(
              [
                  'conditions' => 'userId =?1 AND id =?2',
                  'bind' => [
                      1 => $userId,
                      2 => $id,
                  ],
              ]
            );
            if ($systemicItems) {
                foreach ($systemicItems as $systemicItem) {
                  $systemicChain = SystemicMapChain::find(
                    [
                        'conditions' => 'from_item =?1 OR to_item =?1',
                        'bind' => [
                            1 => $systemicItem->id
                        ],
                    ]
                  );
                  if($systemicChain){
                    $response = [
                      'code' => 0,
                      'status' => 'You cannot delete this systemic item!',
                    ];
                  }else{
                    $systemicItem->delete();
                    $response = [
                      'code' => 1,
                      'status' => 'Success',
                    ];
                  }
                }
            } else {
                $response = [
                  'code' => 0,
                  'status' => 'You cannot delete this systemic item!',
                ];
            }
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function getSystemicItemTree($id)
     {

      $sql="SELECT to_item as id FROM `systemic_map_chain` WHERE from_item Is NULL AND to_item IN (SELECT id FROM systemic_map_items WHERE systemic_map_id=".$id.")";
       $connection = $this->db;
       $data       = $connection->query($sql);
       $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
       $results    = $data->fetchAll();
       $tree=array();
       $this->fillArray($tree,$results);
       return $this->createArrayResponse($tree, 'data');
     }

    public function fillArray(&$tree,$arrayData){
         foreach ($arrayData as $value_first) {
             $sql="SELECT * FROM systemic_map_items WHERE id=".$value_first['id'];
             $connection = $this->db;
             $data       = $connection->query($sql);
             $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
             $iresults   = $data->fetchAll();
             foreach ($iresults as &$item)
             {
               $tree[]= $item;
             }
             foreach ($tree as &$item)
             {
                $id=$item['id'];
                $sql="SELECT to_item as id FROM `systemic_map_chain` WHERE from_item = ".$id;
                $connection = $this->db;
                $data       = $connection->query($sql);
                $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                $ids   = $data->fetchAll();
                $item['items']=array();
                $this->fillArray($item['items'],$ids);
             }
         }
     }
}
