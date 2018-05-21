<?php

namespace App\Controllers;

use App\Model\SystemicStructureMap;
use App\Model\SystemicStructureMapChain;
use App\Model\SystemicStructureMapItems;
use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\SystemicMap;
use App\Model\SystemicMapItems;
use App\Model\SystemicMapChain;
use App\Model\ActionListGroup;
use Phalcon\Http\Request;
use App\Constants\AclRoles;
use Phalcon\Mvc\Model\Query;
use App\Model\Group;

// include '/var/www/html/Classes/PHPExcel.php';
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


    public function getSystemicMapByProcess($id)
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
            'conditions' => '	organization = ?1 AND processId = ?2',
            'bind' => [
                1 => $organization_id,
                2 => $id,
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

    public function getSystemicStructureMapByProcess($id)
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

        $systemicMaps = SystemicStructureMap::find(
            [
                'conditions' => '	organization = ?1 AND processId = ?2',
                'bind' => [
                    1 => $organization_id,
                    2 => $id,
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
                    'startDate' => $systemicMap->startDate,
                    'endDate' => $systemicMap->endDate,
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

    private function findItemIndexForId($arr, $id)
    {
        $index = 0;
        foreach ($arr as $item) {
            if ($item['id'] == $id) {
                break;
            }
            ++$index;
        }

        return $index;
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
                $groupColorValue = null;
                if (isset($systemicMap->groupId)) {
                    $groupColor = Group::findFirst(
                  [
                      'conditions' => 'id = ?1',
                      'bind' => [
                          1 => $systemicMap->groupId,
                      ],
                  ]);
                  // var_dump($groupColorValue = $groupColor->color);die();
                  if ($groupColor->color != null) {
                      $groupColorValue = $groupColor->color;
                  }
                }

                $systemicMapsArray[] = array(
                'id' => $systemicMap->id,
                'systemic_map_id' => $systemicMap->systemic_map_id,
                'name' => $systemicMap->question,
                'proposal' => $systemicMap->proposal,
                'group' => intval($systemicMap->groupId),
                'groupColor' => $groupColorValue,
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
                'source' => $this->findItemIndexForId($systemicMapsArray, intval($chain->from_item)),
                'target' => $this->findItemIndexForId($systemicMapsArray, intval($chain->to_item)),
                'value' => 2,
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
        $systemicMap->processId = $data->processId;
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

        if ($data->proposal == '') {
            $dp = '-';
        } else {
            $dp = $data->proposal;
        }

        $systemicItem = new \App\Model\SystemicMapItems();
        $systemicItem->systemic_map_id = $data->systemic_map_id;
        $systemicItem->question = $data->question;
        $systemicItem->proposal = $dp;
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
                  1 => $userId,
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
                            1 => $systemicItem->id,
                        ],
                    ]
                  );
                    if ($systemicChain) {
                        $response = [
                      'code' => 0,
                      'status' => 'You cannot delete this systemic item!',
                    ];
                    } else {
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
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
          //  echo $creatorId;die();
        }

        $systemicMapsR = SystemicMap::find(
        [
            'conditions' => '	id = ?1',
            'bind' => [
                1 => $id,
            ],
        ]
        );

        if ($systemicMapsR) {
            foreach ($systemicMapsR as $systemicMapR) {
                $systemicR = $systemicMapR->name;
            }
        }

        $creator = $this->getUserDetails($creatorId);
        $creatorInfo = array($creatorId, $creator['account']->role);
        //  var_dump($creator['account']->role);die();
        $connection = $this->db;
        $sql_dist = 'SELECT s1.id,u.first_name,u.last_name FROM `systemic_map_items` s1 JOIN user u ON s1.userId = u.id WHERE s1.id NOT IN (SELECT distinct s2.from_item as id FROM `systemic_map_chain` s2 WHERE s2.from_item IS NOT NULL ) AND s1.systemic_map_id='.$id.'';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();

        $non_ch = array();
        foreach ($results_dist as $key => $value) {
            //  print_r($value);die();
  $first_name_f = $value['first_name'];
            $last_name_f = $value['last_name'];
            $non_ch[] = $value['id'];
        }
// print_r($non_ch);die();
      $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item Is NULL AND to_item IN (SELECT id FROM systemic_map_items WHERE systemic_map_id='.$id.')';

        $data = $connection->query($sql);
        $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $results = $data->fetchAll();
        $tree = array();
        $this->fillArray($tree, $results);
        $htmlcontent = '

        <li class="dd-item dd3-item item'.$tree[0]['id']." generals-item \" style=\“color:".$tree[0]['color'].";\”  data-id=\"".$tree[0]['id'].'">
                  <div class="dd3-content" >
<div class="itemscolor" style="background-color:'.$this->color_luminance($tree[0]['color'], 0.1).'"></div>
                    '.$tree[0]['question'].'

                    <span class="pull-right">


                    <a class="fa fa-lg fa-plus" data-toggle="modal" data-target="#myModal'.$tree[0]['id'].'C"></a>
                    <a class="fa fa-lg fa-pencil-square-o" data-toggle="modal" data-target="#myModal'.$tree[0]['id'].'E"></a>


                    </span>
                  </div>';
 // print_r($tree[0]['items']);die();
// print_r();die();
 $htmlcontent = $this->array_depth($tree[0]['items'], $htmlcontent, $creatorInfo, $non_ch)['html'];
 // $htmlcontent = $htmlcontent2['html']
 $htmlcontent .= '
				<data-sys-map-items-add lolo="myModal" add-func="addSysMapItem('.$tree[0]['id'].',question,proposal,group,color)" datasp="'.$tree[0]['id'].'"></data-sys-map-items-add>

				<data-sys-map-items-edit lolo="myModal" edit-func="editSysMapItem('.$tree[0]['id'].',question,proposal,group,color)" datasp="'.$tree[0]['id'].'" dataprop="'.$tree[0]['proposal'].'" dataque="'.$tree[0]['question'].'" datagrp="'.$tree[0]['groupId'].'" dataclr="'.$tree[0]['color'].'"></data-sys-map-items-edit>

</li>
';
 // echo $htmlcontent;
 // die();
// echo $htmlcontent;die();
$a = array(
  'tree' => $tree,
  'htmlCode' => $htmlcontent,
  'systemic_map_title' => $systemicR,
);

        return $this->createArrayResponse($a, 'data');
    }

    public function fillArray(&$tree, $arrayData)
    {
        foreach ($arrayData as $value_first) {
            $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm JOIN user u ON sm.userId = u.id WHERE sm.id='.$value_first['id'];
// echo $sql;die();
            $connection = $this->db;
            $data = $connection->query($sql);
            $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
            $iresults = $data->fetchAll();
            foreach ($iresults as &$item) {
                $groupTitle = '';
                $groupColorValue = null;
                if (isset($item['groupId'])) {
                    $groupColor = Group::findFirst(
                                  [
                                      'conditions' => 'id = ?1',
                                      'bind' => [
                                          1 => $item['groupId'],
                                      ],
                                  ]);
                                  // var_dump($groupColorValue = $groupColor->color);die();
                                  if ($groupColor->color != null) {
                                      $groupColorValue = $groupColor->color;
                                  }

                    $groupTitle = $groupColor->title;
                }
                            //  $color = array();
                $item['color'] = $groupColorValue;
                $item['groupTitle'] = $groupTitle;

                            //  array_push($item,$color);
               $tree[] = $item;
            //   print_r($tree);die();
            }
            foreach ($tree as &$item) {
                $id = $item['id'];
                $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item = '.$id;
                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                $ids = $data->fetchAll();
                $item['items'] = array();
                $this->fillArray($item['items'], $ids);
            }
        }
    }

    public function array_depth(array $array, &$htmlcontent, $creatorInfo, $non_ch)
    {
        // echo $htmlcontent;
       $max_depth = 1;

        foreach ($array as $value) {
            // print_r($value);die();
          // echo $value['id'];
          // die();
           if (is_array($value)) {
               //print_r($value);

             if (isset($value['id'])) {
                 // echo $value['id'];

              //  $htmlcontent.=$value['id']."***";
              if ((AclRoles::ADMINISTRATOR === $creatorInfo[1]) || (AclRoles::MANAGER === $creatorInfo[1])) {
                  $delete_raw = '<a class="fa fa-lg fa-trash-o" ng-click="deleteSysMapItem('.$value['id'].')"></a>';
              } else {
                  if ($creatorInfo[0] == $value['userId']) {
                      $delete_raw = '<a class="fa fa-lg fa-trash-o" ng-click="deleteSysMapItem('.$value['id'].')"></a>';
                  } else {
                      $delete_raw = '';
                  }
              }

                 if (!in_array($value['id'], $non_ch)) {
                     $delete_raw = '';
                 }

              //  echo $value['id']." <--> ";
               $htmlcontent .= '<ol class="dd-list"> <li class="dd-item dd3-item item'.$value['id']." generals-item\" style=\“color:".$value['color'].";\” data-id=\"".$value['id'].'">
                          <div class="dd3-content" >
                          <div class="itemscolor" style="background-color:'.$this->color_luminance($value['color'], 0.1).'"></div>
                              '.$value['question'].'

                              <span class="pull-right">'.$delete_raw.'


                              <a class="fa fa-lg fa-plus" data-toggle="modal" data-target="#myModal'.$value['id'].'C"></a>
                              <a class="fa fa-lg fa-pencil-square-o" data-toggle="modal" data-target="#myModal'.$value['id'].'E"></a>
                              </span>

                              <data-sys-map-items-add lolo="myModal" add-func="addSysMapItem('.$value['id'].',question,proposal,group,color)" datasp="'.$value['id'].'"></data-sys-map-items-add>

                              <data-sys-map-items-edit lolo="myModal" edit-func="editSysMapItem('.$value['id'].',question,proposal,group,color)" datasp="'.$value['id'].'" dataprop="'.$value['proposal'].'" dataque="'.$value['question'].'" datagrp="'.$value['groupId'].'" dataclr="'.$value['color'].'"></data-sys-map-items-edit>
                              <div style="color: #3276b1;font-size: 12px;" class="item-infos "><strong>by: </strong>'.$value['first_name'].' '.$value['last_name'].'</div>
                              <div class="item-groupname">'.$value['groupTitle'].'</div>
                          </div>';
                          // if($value['id']==98){
                          //   $h=98;
                          //   echo "**********************";
                          //   // die();
                          // }
             }

               $depth = $this->array_depth($value['items'], $htmlcontent, $creatorInfo, $non_ch)['max'] + 1;

              //  echo $depth;die();
               if ($depth > $max_depth) {
                   $max_depth = $depth;
               }

              //die();
              $htmlcontent .= '</li></ol>';
           }
        }
        if (isset($h)) {
            //     echo $htmlcontent;
      // die();
        } else {
        }
        $a = array('max' => $max_depth, 'html' => $htmlcontent);

        return $a;

       //die();
    }

    public function color_luminance($hex, $percent)
    {
        return $hex;
        // validate hex string

        $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
        $new_hex = '#';

        if (strlen($hex) < 6) {
            $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
        }

        // convert to decimal and change luminosity
        for ($i = 0; $i < 3; ++$i) {
            $dec = hexdec(substr($hex, $i * 2, 2));
            $dec = min(max(0, $dec + $dec * $percent), 255);
            $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
        }

        return $new_hex;
    }

    public function createActionListGroup2()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        $creatorInfo = array($creatorId, $creator['account']->role);
        if ($creatorInfo[1] != AclRoles::MANAGER) {
            $response = [
          'code' => 0,
          'status' => 'You cannot delete this systemic item!',
        ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();

            if ($creator['organization'] == null) {
                $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => "Manager's organization not found!",
          ];

                return $this->createArrayResponse($response, 'data');
            }
            $organization_id = $creator['organization']->organization_id;

            $systemicMaps = SystemicMap::findFirst(
          [
              'conditions' => 'id = ?2 AND	organization = ?1',
              'bind' => [
                  1 => $organization_id,
                  2 => $data->systemicMapId,
              ],
          ]
        );
            $systemicMapsArray = array();

            if ($systemicMaps->id) {

          //1.save group details
          $action_grp_list = new \App\Model\ActionListGroup();
                $action_grp_list->systemic_map_id = $systemicMaps->id;
                $action_grp_list->title = $data->title;
                $action_grp_list->created_by = $creatorId;
                if (isset($data->description)) {
                    $action_grp_list->description = $data->description;
                }
                if ($action_grp_list->save() == false) {
                    $messagesErrors = array();
                    foreach ($action_grp_list->getMessages() as $message) {
                        // print_r($message);
                  $messagesErrors[] = $message;
                    }
                    $response = [
                  'code' => 0,
                  'status' => 'Error',
                  'data' => $messagesErrors,
              ];
                } else {
                    $action_grp_list_id = $action_grp_list->getWriteConnection()->lastInsertId();

            //  2.copy sam as action list
            $connection = $this->db;
                    $sql_dist = 'SELECT s1.id,u.first_name,u.last_name FROM `systemic_map_items` s1 JOIN user u ON s1.userId = u.id WHERE s1.id NOT IN (SELECT distinct s2.from_item as id FROM `systemic_map_chain` s2 WHERE s2.from_item IS NOT NULL ) AND s1.systemic_map_id='.$systemicMaps->id.'';
                    $data_dist = $connection->query($sql_dist);
                    $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                    $results_dist = $data_dist->fetchAll();

                    $non_ch = array();
                    foreach ($results_dist as $key => $value) {
                        //  print_r($value);die();
      $first_name_f = $value['first_name'];
                        $last_name_f = $value['last_name'];
                        $non_ch[] = $value['id'];
                    }
    // print_r($non_ch);die();
          $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item Is NULL AND to_item IN (SELECT id FROM systemic_map_items WHERE systemic_map_id='.$systemicMaps->id.')';

                    $data = $connection->query($sql);
                    $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                    $results = $data->fetchAll();
                    $tree = array();
                    $this->fillArray($tree, $results);
                    print_r($tree);
            // print_r(array_reverse($tree));

die();

              // $response = [
              //   'code' => 1,
              //   'status' => 'Success',
              //   'data' => array('systemicMapId' => $action_grp_list_id),
              // ];
                }
            } else {
                $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Systemic Map not found',
          ];
            }
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function createActionListGroup()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        $creatorInfo = array($creatorId, $creator['account']->role);
        if ($creatorInfo[1] != AclRoles::MANAGER) {
            $response = [
          'code' => 0,
          'status' => 'You cannot delete this systemic item!',
        ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();
            $creator = $this->getUserDetails($creatorId);
            $creatorInfo = array($creatorId, $creator['account']->role);
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS (SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = '.$data->systemicMapId.' ';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();
            $out_nodes = array();
            $tree = array();

            $this->fillArray2($tree, $results_dist);

        // print_r($tree);
        $a = array(
          'tree' => $tree,
        );

            return $this->createArrayResponse($a, 'data');
        }
    }

    public function fillArray2(&$tree, $arrayData)
    {
        foreach ($arrayData as $value_first) {
            if (($value_first['id'] != '')) {
                $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm JOIN user u ON sm.userId = u.id WHERE sm.id='.$value_first['id'];

                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                $iresults = $data->fetchAll();
                foreach ($iresults as &$item) {
                    $groupTitle = '';
                    $groupColorValue = null;
                    if (isset($item['groupId'])) {
                        $groupColor = Group::findFirst(
                                  [
                                      'conditions' => 'id = ?1',
                                      'bind' => [
                                          1 => $item['groupId'],
                                      ],
                                  ]);
                                  // var_dump($groupColorValue = $groupColor->color);die();
                                  if ($groupColor->color != null) {
                                      $groupColorValue = $groupColor->color;
                                  }

                        $groupTitle = $groupColor->title;
                    }
                            //  $color = array();
                $item['color'] = $groupColorValue;
                    $item['groupTitle'] = $groupTitle;

                            //  array_push($item,$color);
               $tree[] = $item;
            //   print_r($tree);die();
                }
                foreach ($tree as &$item) {
                    $id = $item['id'];
                    $sql = 'SELECT from_item as id FROM `systemic_map_chain` WHERE to_item = '.$id;
                    $connection = $this->db;
                    $data = $connection->query($sql);
                    $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                    $ids = $data->fetchAll();
                    $item['items'] = array();

                // if (!empty($ids)) {
                  $this->fillArray2($item['items'], $ids);
                // }
              //  }
                }
            }
        }
    }

    public function createActionListGroup3()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        $creatorInfo = array($creatorId, $creator['account']->role);
        if ($creatorInfo[1] != AclRoles::MANAGER) {
            $response = [
          'code' => 0,
          'status' => 'You cannot delete this systemic item!',
        ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();
            $creator = $this->getUserDetails($creatorId);
            $creatorInfo = array($creatorId, $creator['account']->role);
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS (SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = '.$data->systemicMapId.' ';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();
            $out_nodes = array();
            $tree = array();

            $this->fillArray3($tree, $results_dist);

        // print_r($tree);
        $a = array(
          'tree' => $tree,
        );

            return $this->createArrayResponse($a, 'data');
        }
    }

    public function fillArray3(&$tree, $arrayData)
    {
        foreach ($arrayData as $value_first) {
            if (($value_first['id'] != '')) {
                $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm JOIN user u ON sm.userId = u.id WHERE sm.id='.$value_first['id'];

                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                $iresults = $data->fetchAll();
                foreach ($iresults as &$item) {
                    $groupTitle = '';
                    $groupColorValue = null;
                    if (isset($item['groupId'])) {
                        $groupColor = Group::findFirst(
                                  [
                                      'conditions' => 'id = ?1',
                                      'bind' => [
                                          1 => $item['groupId'],
                                      ],
                                  ]);
                                  // var_dump($groupColorValue = $groupColor->color);die();
                                  if ($groupColor->color != null) {
                                      $groupColorValue = $groupColor->color;
                                  }

                        $groupTitle = $groupColor->title;
                    }
                            //  $color = array();
                $item['color'] = $groupColorValue;
                    $item['groupTitle'] = $groupTitle;

                            //  array_push($item,$color);
               $tree[] = $item;
            //   print_r($tree);die();
                }
                foreach ($tree as &$item) {
                    $id = $item['id'];
                    $sql = 'SELECT from_item as id FROM `systemic_map_chain` WHERE to_item = '.$id;
                    $connection = $this->db;
                    $data = $connection->query($sql);
                    $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                    $ids = $data->fetchAll();
                    $item['items'] = array();

                // if (!empty($ids)) {
                  $this->fillArray3($item['items'], $ids);
                // }
              //  }
                }
            }
        }
    }

    public function createActionListGroup4()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        $creatorInfo = array($creatorId, $creator['account']->role);
        if ($creatorInfo[1] != AclRoles::MANAGER) {
            $response = [
          'code' => 0,
          'status' => 'You cannot delete this systemic item!',
        ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();
            $creator = $this->getUserDetails($creatorId);
            $creatorInfo = array($creatorId, $creator['account']->role);
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS (SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = '.$data->systemicMapId.' ';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();
            $out_nodes = array();
            $tree = array();

            $this->fillArray3($tree, $results_dist);

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// echo $data->systemicMapId;
$action_grp_list = new ActionListGroup();
            $action_grp_list->systemic_map_id = $data->systemicMapId;
            $action_grp_list->title = $data->title;
            $action_grp_list->created_by = $creatorId;
            if (isset($data->description)) {
                $action_grp_list->description = $data->description;
            }
            if ($action_grp_list->save() == false) {
                $messagesErrors = array();
                foreach ($action_grp_list->getMessages() as $message) {
                    // print_r($message);
        $messagesErrors[] = $message;
                }
                $response = [
        'code' => 0,
        'status' => 'Error',
        'data' => $messagesErrors,
    ];
            }
            $action_grp_list_id = $action_grp_list->getWriteConnection()->lastInsertId();

// echo $action_grp_list_id;

        //  print_r($tree);
// echo $this->deepness($tree[0]);
  foreach ($tree as $key => $value) {
      $length = $this->deepness($value);
      $path = array();
      $last = $value['items'];
      $path[] = array(
      'priority' => -1,
      'data' => $last,
    );
    // $data_dist = $connection->query("");
    for ($i = 0; $i < $length; ++$i) {
        // echo "asda";

      if (!$this->is_array_empty($last)) {
          echo 'sda2';
        // print_r($last);
        //echo $length;
        $path[] = array(
          'priority' => $i,
          'data' => $last,
        );
          $last = $last['items'];
      } else {
          break;
      }
    }
    // print_r($path);
    // die();
  }
            print_r($path);
            die();
        // $a = array(
        //   'tree' => $tree,
        // );

        //         return $this->createArrayResponse($a, 'data');
        }
    }

    public function max_depth($arr)
    {

    // json encode
    $string = json_encode($arr);
    // removing string values to avoid braces in strings
    $string = preg_replace('/\"(.*?)\"/', '""', $string);
    //Replacing object braces with array braces
    $string = str_replace(['{', '}'], ['[', ']'], $string);

        $length = strlen($string);
        $now = $max = 0;

        for ($i = 0; $i < $length; ++$i) {
            if ($string[$i] == '[') {
                ++$now;
                $max = $max < $now ? $now : $max;
            }

            if ($string[$i] == ']') {
                --$now;
            }
        }

        return $max;
    }

    public function deepness(array $arr)
    {
        $exploded = explode(',', json_encode($arr, JSON_FORCE_OBJECT)."\n\n");
        $longest = 0;
        foreach ($exploded as $row) {
            $longest = (substr_count($row, ':') > $longest) ?
            substr_count($row, ':') : $longest;
        }

        return $longest;
    }

    public function is_array_empty($a)
    {
        foreach ($a as $elm) {
            if (!empty($elm)) {
                return false;
            }
        }

        return true;
    }

    public function createSystemicStructureMap()
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
        $systemicStructureMap = new \App\Model\SystemicStructureMap();
        $systemicStructureMap->name = $data->name;
        $systemicStructureMap->by_whom = $creatorId;

        $systemicStructureMap->organization = $organization_id;
        $systemicStructureMap->lang = $data->lang;
        $systemicStructureMap->isActive = $data->isActive;
        $systemicStructureMap->processId = $data->processId;
        $systemicStructureMap->startDate = $data->startDate;
        $systemicStructureMap->endDate = $data->endDate;
        if ($systemicStructureMap->save() == false) {
            $messagesErrors = array();
            foreach ($systemicStructureMap->getMessages() as $message) {
                // print_r($message);
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $systemicMapId = $systemicStructureMap->getWriteConnection()->lastInsertId();
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => array('systemicStructureMapId' => $systemicMapId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }


    public function updateSystemicStructureMap($id)
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
            $systemicStructureMap = \App\Model\SystemicStructureMap::findFirst(
                [
                    'conditions' => 'id = ?1 AND organization = ?2',
                    'bind' => [
                        1 => $id,
                        2 => $organization_id,
                    ],
                ]);
        } else {
            $systemicStructureMap = false;
        }
        if ($systemicStructureMap) {
            if (isset($data->name)) {
                $systemicStructureMap->name = $data->name;
            }
            if (isset($data->startDate)) {
                $systemicStructureMap->startDate = $data->startDate;
            }
            if (isset($data->endDate)) {
                $systemicStructureMap->endDate = $data->endDate;
            }
            if (isset($data->lang)) {
                $systemicStructureMap->lang = $data->lang;
            }
            if (isset($data->isActive)) {
                $systemicStructureMap->isActive = $data->isActive;
            }
            $systemicStructureMap->save();
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


    public function createSystemicStructureMapItem()
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

        if ($data->proposal == '') {
            $dp = '-';
        } else {
            $dp = $data->proposal;
        }

        $systemicStructureItem = new \App\Model\SystemicStructureMapItems();
        $systemicStructureItem->systemic_map_id = $data->systemic_map_id;
        $systemicStructureItem->question = $data->question;
        $systemicStructureItem->proposal = $dp;
        $systemicStructureItem->groupId = $data->groupId;
        $systemicStructureItem->itemType = $data->itemType;
        $systemicStructureItem->userId = $creatorId;
        if ($systemicStructureItem->save() == false) {
            $messagesErrors = array();
            foreach ($systemicStructureItem->getMessages() as $message) {
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
//            $systemicStructureMapItemId = $systemicStructureItem->getWriteConnection()->lastInsertId();
//            $chain = new \App\Model\SystemicStructureMapChain();
//            if ($data->from_item == '') {
//                $chain->from_item = null;
//            } else {
//                $chain->from_item = $data->from_item;
//            }
//            $chain->to_item = $systemicStructureMapItemId;
//            if ($chain->save() == false) {
//                $messagesErrors = array();
//                foreach ($chain->getMessages() as $message) {
//                    $messagesErrors[] = $message;
//                }
//                //die();
//                $response = [
//                    'code' => 0,
//                    'status' => 'Error',
//                    'data' => $messagesErrors,
//                ];
//
//                return $this->createArrayResponse($response, 'data');
//            }

            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => array('systemicStructureMapItemId' => $systemicStructureMapItemId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }


    public function deleteSystemicStructureItem($id)
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
            $systemicChains = SystemicStructureMapChain::find(
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
            $systemicItems = SystemicStructureMapItems::find(
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
            $systemicItems = SystemicStructureMapItems::find(
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
                    $systemicChain = SystemicStructureMapChain::find(
                        [
                            'conditions' => 'from_item =?1 OR to_item =?1',
                            'bind' => [
                                1 => $systemicItem->id,
                            ],
                        ]
                    );
                    if ($systemicChain) {
                        $response = [
                            'code' => 0,
                            'status' => 'You cannot delete this systemic item!',
                        ];
                    } else {
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

    public function getSystemicStructureItem($id,$type)
    {
        $systemicMaps = SystemicStructureMapItems::find(
            [
                'conditions' => 'systemic_map_id = ?1 and itemType = ?2 ',
                'bind' => [
                    1 => $id,
                    2 => $type
                ],
            ]
        );
        $systemicMapsArray = array();
        $linksArray = array();
        if ($systemicMaps) {
            foreach ($systemicMaps as $systemicMap) {
                $groupColorValue = null;
                if (isset($systemicMap->groupId)) {
                    $groupColor = Group::findFirst(
                        [
                            'conditions' => 'id = ?1',
                            'bind' => [
                                1 => $systemicMap->groupId,
                            ],
                        ]);
                    // var_dump($groupColorValue = $groupColor->color);die();
                    if ($groupColor->color != null) {
                        $groupColorValue = $groupColor->color;
                    }
                }

                $systemicMapsArray[] = array(
                    'id' => $systemicMap->id,
                    'systemic_map_id' => $systemicMap->systemic_map_id,
                    'name' => $systemicMap->question,
                    'proposal' => $systemicMap->proposal,
                    'group' => intval($systemicMap->groupId),
                    'groupColor' => $groupColorValue,
                );

                $chains = SystemicStructureMapChain::find(
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
                        'source' => $this->findItemIndexForId($systemicMapsArray, intval($chain->from_item)),
                        'target' => $this->findItemIndexForId($systemicMapsArray, intval($chain->to_item)),
                        'value' => 2,
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

    public function updateSystemicStructureItem($id)
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
            $systemicItems = SystemicStructureMapItems::findFirst(
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
                                1 => $userId,
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
            $systemicItems = SystemicStructureMapItems::findFirst(
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

    public function getStructureChain($id, $itemType){
        $connection = $this->db;
        $sql_dist = 'SELECT DISTINCT SC.id,SC.from_item,SC.to_item,I.itemType FROM `systemic_structure_map_chain` SC left JOIN systemic_map_structure_items I ON SC.from_item = I.id OR SC.to_item = I.id WHERE I.itemType = "'.$itemType.'" AND systemic_map_id = '.$id.' ';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();
        $resultArray = array();
        foreach ($results_dist as $item) {
            $resultArray[] = array(
                "id"=>(int)$item['id'],
                "from_item"=>(int)$item['from_item'],
                "to_item"=>(int)$item['to_item'],
                "itemType"=>$item['itemType']
            );
        }

        return $this->createArrayResponse($resultArray, 'chain');
    }


    public function deleteStructureChain($id){
        $chain = SystemicStructureMapChain::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $id,
                ],
            ]);
        $chain->delete();

        $response = [
            'code' => 1,
            'status' => 'Success',
        ];
        return $this->createArrayResponse($response, 'data');
    }


    public function createStructureChain(){
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

        if(isset($data->from_item)) {
            $systemicMapsItems = SystemicStructureMapItems::findFirst(
                [
                    'conditions' => 'id = ?1 ',
                    'bind' => [
                        1 => $data->from_item
                    ],
                ]
            );

            if (!$systemicMapsItems) {
                $response = [
                    'code' => 0,
                    'status' => 'Cannot find item id: '. $data->from_item,
                ];
                return $this->createArrayResponse($response, 'data');
            }
        }
        $systemicMapsItems2 = SystemicStructureMapItems::findFirst(
            [
                'conditions' => 'id = ?1 ',
                'bind' => [
                    1 => $data->from_item
                ],
            ]
        );

        if(!$systemicMapsItems2){
            $response = [
                'code' => 0,
                'status' => 'Cannot find item id: '. $data->to_item,
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $chain = new SystemicStructureMapChain();
        if(isset($data->from_item)){
            $chain->from_item = $data->from_item;
        }
        $chain->to_item = $data->to_item;
        $chain->save();
        $response = [
            'code' => 1,
            'status' => 'Success',
        ];
        return $this->createArrayResponse($response, 'data');

    }
}
