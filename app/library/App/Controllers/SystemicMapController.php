<?php

namespace App\Controllers;

use App\Model\SystemicStructureMap;
use App\Model\SystemicStructureMapChain;
use App\Model\SystemicStructureMapItems;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\SystemicMap;
use App\Model\SystemicMapItems;
use App\Model\SystemicMapChain;
use App\Model\ActionListGroup;
use Phalcon\Http\Request;
use App\Constants\AclRoles;
use App\Model\Group;

// include '/var/www/html/Classes/PHPExcel.php';
class SystemicMapController extends CrudResourceController
{

    public function getSystemicMap()
    {
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        /** @var Simple $systemicMaps */
        $systemicMaps = SystemicMap::find(
            [
                'conditions' => '	organization = ?1',
                'bind' => [
                    1 => $organization_id,
                ],
            ]
        );
        $systemicMapsArray = [];
        if ($systemicMaps->count() > 0) {
            /** @var SystemicMap $systemicMap */
            foreach ($systemicMaps as $systemicMap) {
                $systemicMapsArray[] = [
                    'id' => $systemicMap->id,
                    'name' => $systemicMap->name,
                    'department' => $systemicMap->department,
                    'organization' => $systemicMap->organization,
                    'isActive' => $systemicMap->isActive,
                ];
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        /** @var Simple $systemicMaps */
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
        if ($systemicMaps->count() > 0) {
            /** @var SystemicMap $systemicMap */
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        /** @var Simple $systemicMaps */
        $systemicMaps = SystemicStructureMap::find(
            [
                'conditions' => '	organization = ?1 AND processId = ?2',
                'bind' => [
                    1 => $organization_id,
                    2 => $id,
                ],
            ]
        );
        $systemicMapsArray = [];
        if ($systemicMaps->count() > 0) {
            /** @var SystemicStructureMap $systemicMap */
            foreach ($systemicMaps as $systemicMap) {
                $systemicMapsArray[] = [
                    'id' => $systemicMap->id,
                    'name' => $systemicMap->name,
                    'department' => $systemicMap->department,//todo
                    'organization' => $systemicMap->organization,
                    'startDate' => $systemicMap->startDate,
                    'endDate' => $systemicMap->endDate,
                    'isActive' => $systemicMap->isActive,
                ];
            }
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $systemicMapsArray,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    private function findItemIndexForId(array $arr, $id)
    {
        $index = 0;
        foreach ($arr as $item) {
            if ($item['id'] === $id) {
                break;
            }
            ++$index;
        }

        return $index;
    }

    public function getSystemicItem($id)
    {
        /** @var Simple $systemicMaps */
        $systemicMaps = SystemicMapItems::find(
            [
                'conditions' => '	systemic_map_id = ?1',
                'bind' => [
                    1 => $id,
                ],
            ]
        );
        $systemicMapsArray = [];
        $linksArray = [];
        if ($systemicMaps->count() > 0) {
            /** @var SystemicMapItems $systemicMap */
            foreach ($systemicMaps as $systemicMap) {
                $groupColorValue = null;
                if ($systemicMap->groupId !== null) {
                    $groupColor = Group::findFirst(
                        [
                            'conditions' => 'id = ?1',
                            'bind' => [
                                1 => $systemicMap->groupId,
                            ],
                        ]
                    );
                    if ($groupColor instanceof Group && $groupColor->color !== null) {
                        $groupColorValue = $groupColor->color;
                    }
                }

                $systemicMapsArray[] = [
                    'id' => $systemicMap->id,
                    'systemic_map_id' => $systemicMap->systemic_map_id,
                    'name' => $systemicMap->question,
                    'proposal' => $systemicMap->proposal,
                    'group' => (int) $systemicMap->groupId,
                    'groupColor' => $groupColorValue,
                ];

                /** @var Simple $chains */
                $chains = SystemicMapChain::find(
                    [
                        'conditions' => 'to_item =?1',
                        'bind' => [
                            1 => $systemicMap->id,
                        ],
                    ]
                );

                /** @var SystemicMapChain $chain */
                foreach ($chains as $chain) {
                    $linksArray[] = array(
                        'source' => $this->findItemIndexForId($systemicMapsArray, (int) $chain->from_item),
                        'target' => $this->findItemIndexForId($systemicMapsArray, (int) $chain->to_item),
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
            $mandatory = $validate[$key] ?? false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
                'code' => 0,
                'status' => 'Required field: ' . implode(', ', $missing_input),
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $systemicMap = new SystemicMap();
        $systemicMap->name = $data->name;
        if ($data->department === '') {
            $systemicMap->department = null;
        } else {
            $systemicMap->department = $data->department;
        }

        $systemicMap->organization = $organization_id;
        $systemicMap->lang = $data->lang;
        $systemicMap->isActive = $data->isActive;
        $systemicMap->processId = $data->processId;
        if ($systemicMap->save() === false) {
            $messagesErrors = array();
            foreach ($systemicMap->getMessages() as $message) {
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $request = new Request();
        $data = $request->getJsonRawBody();

        $validate = [
            'systemic_map_id' => ['mandatory' => true, 'regex' => null],
            'question' => ['mandatory' => true, 'regex' => null],
        ];

        $missing_input = array();

        foreach ($data as $key => $val) {
            $mandatory = $validate[$key] ?? false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
                'code' => 0,
                'status' => 'Required field: ' . implode(', ', $missing_input),
            ];

            return $this->createArrayResponse($response, 'data');
        }

        if ($data->proposal === '') {
            $dp = '-';
        } else {
            $dp = $data->proposal;
        }

        $systemicItem = new SystemicMapItems();
        $systemicItem->systemic_map_id = $data->systemic_map_id;
        $systemicItem->question = $data->question;
        $systemicItem->proposal = $dp;
        $systemicItem->groupId = $data->groupId;
        $systemicItem->userId = $creatorId;
        if ($systemicItem->save() === false) {
            $messagesErrors = array();
            foreach ($systemicItem->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $systemicMapItemId = $systemicItem->getWriteConnection()->lastInsertId();
            $chain = new SystemicMapChain();
            if ($data->from_item === '') {
                $chain->from_item = null;
            } else {
                $chain->from_item = $data->from_item;
            }
            $chain->to_item = $systemicMapItemId;
            if ($chain->save() === false) {
                $messagesErrors = array();
                foreach ($chain->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
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
            }
            return array('account' => $user, 'organization' => null);
        }
        return null;
    }

    public function updateSystemicMap($id)
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
                    1 => $creatorId,
                ],
            ]
        );

        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicMap = SystemicMap::findFirst(
                [
                    'conditions' => 'id = ?1 AND organization = ?2',
                    'bind' => [
                        1 => $id,
                        2 => $organization_id,
                    ],
                ]
            );
        } else {
            $systemicMap = false;
        }
        if ($systemicMap instanceof SystemicMap) {
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
                    1 => $creatorId,
                ],
            ]
        );

        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicItems = SystemicMapItems::findFirst(
                [
                    'conditions' => 'id = ?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );

            if ($systemicItems instanceof SystemicMapItems && $creatorId !== $systemicItems->userId) {
                $organizationChecked = UserOrganization::findFirst(
                    [
                        'conditions' => 'user_id = ?1 AND organization_id = ?2',
                        'bind' => [
                            1 => $creatorId,
                            2 => $organization_id,
                        ],
                    ]
                );

                if (!($organizationChecked instanceof UserOrganization)) {
                    $response = [
                        'code' => 0,
                        'status' => 'You cannot edit this group2!',
                    ];

                    return $this->createArrayResponse($response, 'data');
                }
            }
        } else {
            $systemicItems = SystemicMapItems::findFirst(
                [
                    'conditions' => 'id = ?1 AND userId = ?2',
                    'bind' => [
                        1 => $id,
                        2 => $creatorId,
                    ],
                ]
            );
        }
        if ($systemicItems instanceof SystemicMapItems) {
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator ? $creator['organization']->organization_id : null;
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $creatorId,
                ],
            ]
        );
        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicMap = SystemicMap::findFirst(
                [
                    'conditions' => 'id = ?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );

            if ($systemicMap instanceof SystemicMap) {
                /** @var Simple $systemicChains */
                $systemicChains = SystemicMapChain::find(
                    [
                        'conditions' => 'from_item =?1 OR to_item =?1',
                        'bind' => [
                            1 => $systemicMap->id,
                        ],
                    ]
                );
                /** @var SystemicMapChain $systemicChain */
                foreach ($systemicChains as $systemicChain) {
                    $systemicChain->delete();
                }
                /** @var Simple $systemicItems */
                $systemicItems = SystemicMapItems::find(
                    [
                        'conditions' => 'systemic_map_id =?1',
                        'bind' => [
                            1 => $systemicMap->id,
                        ],
                    ]
                );
                /** @var SystemicMapItems $systemicItem */
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
                    1 => $creatorId,
                ],
            ]
        );
        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            /** @var Simple $systemicChains */
            $systemicChains = SystemicMapChain::find(
                [
                    'conditions' => 'from_item =?1 OR to_item =?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );
            /** @var SystemicMapChain $systemicChain */
            foreach ($systemicChains as $systemicChain) {
                $systemicChain->delete();
            }
            /** @var Simple $systemicItems */
            $systemicItems = SystemicMapItems::find(
                [
                    'conditions' => 'id =?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );
            /** @var SystemicMapItems $systemicItem */
            foreach ($systemicItems as $systemicItem) {
                $systemicItem->delete();
            }

            $response = [
                'code' => 1,
                'status' => 'Success!',
            ];
        } else {
            /** @var Simple $systemicItems */
            $systemicItems = SystemicMapItems::find(
                [
                    'conditions' => 'userId =?1 AND id =?2',
                    'bind' => [
                        1 => $creatorId,
                        2 => $id,
                    ],
                ]
            );
            if ($systemicItems->count() > 0) {
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                ];
                /** @var SystemicMapItems $systemicItem */
                foreach ($systemicItems as $systemicItem) {
                    /** @var Simple $systemicChain */
                    $systemicChain = SystemicMapChain::find(
                        [
                            'conditions' => 'from_item =?1 OR to_item =?1',
                            'bind' => [
                                1 => $systemicItem->id,
                            ],
                        ]
                    );
                    if ($systemicChain->count() > 0) {
                        $response = [
                            'code' => 0,
                            'status' => 'You cannot delete this systemic item!',
                        ];
                    } else {
                        $systemicItem->delete();
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        /** @var Simple $systemicMapsR */
        $systemicMapsR = SystemicMap::find(
            [
                'conditions' => '	id = ?1',
                'bind' => [
                    1 => $id,
                ],
            ]
        );

        if ($systemicMapsR->count() > 0) {
            /** @var SystemicMap $systemicMapR */
            foreach ($systemicMapsR as $systemicMapR) {
                $systemicR = $systemicMapR->name;
            }
        }

        $creator = static::getUserDetails($creatorId);
        $creatorInfo = array($creatorId, $creator['account']->role);
        $connection = $this->db;
        $sql_dist = 'SELECT s1.id,u.first_name,u.last_name FROM `systemic_map_items` s1 JOIN user u ON s1.userId = u.id WHERE s1.id NOT IN (SELECT distinct s2.from_item as id FROM `systemic_map_chain` s2 WHERE s2.from_item IS NOT NULL ) AND s1.systemic_map_id=' . $id . '';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();

        $non_ch = array();
        foreach ($results_dist as $key => $value) {
            $first_name_f = $value['first_name'];
            $last_name_f = $value['last_name'];
            $non_ch[] = $value['id'];
        }
        $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item Is NULL AND to_item IN (SELECT id FROM systemic_map_items WHERE systemic_map_id=' . $id . ')';

        $data = $connection->query($sql);
        $data->setFetchMode(Db::FETCH_ASSOC);
        $results = $data->fetchAll();
        $tree = array();
        $this->fillArray($tree, $results);
        $htmlcontent = '

        <li class="dd-item dd3-item item' . $tree[0]['id'] . " generals-item \" style=\“color:" . $tree[0]['color'] . ";\”  data-id=\"" . $tree[0]['id'] . '">
                  <div class="dd3-content" >
<div class="itemscolor" style="background-color:' . $this->colorLuminance($tree[0]['color'], 0.1) . '"></div>
                    ' . $tree[0]['question'] . '

                    <span class="pull-right">


                    <a class="fa fa-lg fa-plus" data-toggle="modal" data-target="#myModal' . $tree[0]['id'] . 'C"></a>
                    <a class="fa fa-lg fa-pencil-square-o" data-toggle="modal" data-target="#myModal' . $tree[0]['id'] . 'E"></a>


                    </span>
                  </div>';
        $htmlcontent = $this->arrayDepth($tree[0]['items'], $htmlcontent, $creatorInfo, $non_ch)['html'];
        // $htmlcontent = $htmlcontent2['html']
        $htmlcontent .= '
				<data-sys-map-items-add lolo="myModal" add-func="addSysMapItem(' . $tree[0]['id'] . ',question,proposal,group,color)" datasp="' . $tree[0]['id'] . '"></data-sys-map-items-add>

				<data-sys-map-items-edit lolo="myModal" edit-func="editSysMapItem(' . $tree[0]['id'] . ',question,proposal,group,color)" datasp="' . $tree[0]['id'] . '" dataprop="' . $tree[0]['proposal'] . '" dataque="' . $tree[0]['question'] . '" datagrp="' . $tree[0]['groupId'] . '" dataclr="' . $tree[0]['color'] . '"></data-sys-map-items-edit>

</li>
';
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
            $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm JOIN user u ON sm.userId = u.id WHERE sm.id=' . $value_first['id'];

            $connection = $this->db;
            $data = $connection->query($sql);
            $data->setFetchMode(Db::FETCH_ASSOC);
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
                        ]
                    );
                    if ($groupColor instanceof Group && $groupColor->color !== null) {
                        $groupColorValue = $groupColor->color;
                    }

                    $groupTitle = $groupColor->title;
                }
                //  $color = array();
                $item['color'] = $groupColorValue;
                $item['groupTitle'] = $groupTitle;

                //  array_push($item,$color);
                $tree[] = $item;
            }
            foreach ($tree as &$item) {
                $id = $item['id'];
                $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item = ' . $id;
                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(Db::FETCH_ASSOC);
                $ids = $data->fetchAll();
                $item['items'] = array();
                $this->fillArray($item['items'], $ids);
            }
        }
    }

    public function arrayDepth(array $array, &$htmlcontent, $creatorInfo, $non_ch)
    {
        $max_depth = 1;

        foreach ($array as $value) {
            if (\is_array($value)) {
                if (isset($value['id'])) {
                    //  $htmlcontent.=$value['id']."***";
                    if ((AclRoles::ADMINISTRATOR === $creatorInfo[1]) || (AclRoles::MANAGER === $creatorInfo[1])) {
                        $delete_raw = '<a class="fa fa-lg fa-trash-o" ng-click="deleteSysMapItem(' . $value['id'] . ')"></a>';
                    } else {
                        if ($creatorInfo[0] == $value['userId']) {
                            $delete_raw = '<a class="fa fa-lg fa-trash-o" ng-click="deleteSysMapItem(' . $value['id'] . ')"></a>';
                        } else {
                            $delete_raw = '';
                        }
                    }

                    if (!\in_array($value['id'], $non_ch, true)) {
                        $delete_raw = '';
                    }
                    $htmlcontent .= '<ol class="dd-list"> <li class="dd-item dd3-item item' . $value['id'] . " generals-item\" style=\“color:" . $value['color'] . ";\” data-id=\"" . $value['id'] . '">
                          <div class="dd3-content" >
                          <div class="itemscolor" style="background-color:' . $this->colorLuminance($value['color'], 0.1) . '"></div>
                              ' . $value['question'] . '

                              <span class="pull-right">' . $delete_raw . '


                              <a class="fa fa-lg fa-plus" data-toggle="modal" data-target="#myModal' . $value['id'] . 'C"></a>
                              <a class="fa fa-lg fa-pencil-square-o" data-toggle="modal" data-target="#myModal' . $value['id'] . 'E"></a>
                              </span>

                              <data-sys-map-items-add lolo="myModal" add-func="addSysMapItem(' . $value['id'] . ',question,proposal,group,color)" datasp="' . $value['id'] . '"></data-sys-map-items-add>

                              <data-sys-map-items-edit lolo="myModal" edit-func="editSysMapItem(' . $value['id'] . ',question,proposal,group,color)" datasp="' . $value['id'] . '" dataprop="' . $value['proposal'] . '" dataque="' . $value['question'] . '" datagrp="' . $value['groupId'] . '" dataclr="' . $value['color'] . '"></data-sys-map-items-edit>
                              <div style="color: #3276b1;font-size: 12px;" class="item-infos "><strong>by: </strong>' . $value['first_name'] . ' ' . $value['last_name'] . '</div>
                              <div class="item-groupname">' . $value['groupTitle'] . '</div>
                          </div>';
                    // if($value['id']==98){
                    //   $h=98;
                    //   echo "**********************";
                    //   // die();
                    // }
                }

                $depth = $this->arrayDepth($value['items'], $htmlcontent, $creatorInfo, $non_ch)['max'] + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }

                $htmlcontent .= '</li></ol>';
            }
        }
        
        return ['max' => $max_depth, 'html' => $htmlcontent];
    }

    public function colorLuminance($hex, $percent)
    {
        return $hex;//todo
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        $creatorInfo = null;
        if ($creator) {
            $creatorInfo = [$creatorId, $creator['account']->role];
        }
        if ($creator && $creatorInfo[1] !== AclRoles::MANAGER) {
            $response = [
                'code' => 0,
                'status' => 'You cannot delete this systemic item!',
            ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();

            if ($creator && $creator['organization'] === null) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => "Manager's organization not found!",
                ];

                return $this->createArrayResponse($response, 'data');
            }
            $organization_id = $creator ? $creator['organization']->organization_id : 0;

            $systemicMaps = SystemicMap::findFirst(
                [
                    'conditions' => 'id = ?2 AND	organization = ?1',
                    'bind' => [
                        1 => $organization_id,
                        2 => $data->systemicMapId,
                    ],
                ]
            );
            $systemicMapsArray = [];

            if ($systemicMaps instanceof SystemicMap) {
                //1.save group details
                $action_grp_list = new ActionListGroup();
                $action_grp_list->systemic_map_id = $systemicMaps->id;
                $action_grp_list->title = $data->title;
                $action_grp_list->created_by = $creatorId;
                if (isset($data->description)) {
                    $action_grp_list->description = $data->description;
                }
                if ($action_grp_list->save() === false) {
                    $messagesErrors = [];
                    foreach ($action_grp_list->getMessages() as $message) {
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
                    $sql_dist = 'SELECT s1.id,u.first_name,u.last_name FROM `systemic_map_items` s1 JOIN user u ON s1.userId = u.id WHERE s1.id NOT IN (SELECT distinct s2.from_item as id FROM `systemic_map_chain` s2 WHERE s2.from_item IS NOT NULL ) AND s1.systemic_map_id=' . $systemicMaps->id . '';
                    $data_dist = $connection->query($sql_dist);
                    $data_dist->setFetchMode(Db::FETCH_ASSOC);
                    $results_dist = $data_dist->fetchAll();

                    $non_ch = array();
                    foreach ($results_dist as $key => $value) {
                        //  print_r($value);die();
                        $first_name_f = $value['first_name'];
                        $last_name_f = $value['last_name'];
                        $non_ch[] = $value['id'];
                    }
                    // print_r($non_ch);die();
                    $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item Is NULL AND to_item IN (SELECT id FROM systemic_map_items WHERE systemic_map_id=' . $systemicMaps->id . ')';

                    $data = $connection->query($sql);
                    $data->setFetchMode(Db::FETCH_ASSOC);
                    $results = $data->fetchAll();
                    $tree = array();
                    $this->fillArray($tree, $results);

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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        $creatorInfo = null;
        if ($creator) {
            $creatorInfo = [$creatorId, $creator['account']->role];
        }

        if ($creator &&$creatorInfo[1] !== AclRoles::MANAGER) {
            $response = [
                'code' => 0,
                'status' => 'You cannot delete this systemic item!',
            ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS (SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = ' . $data->systemicMapId . ' ';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();
            $out_nodes = [];
            $tree = [];

            $this->fillArray2($tree, $results_dist);

            // print_r($tree);
            $a = [
                'tree' => $tree,
            ];

            return $this->createArrayResponse($a, 'data');
        }
    }

    public function fillArray2(&$tree, array $arrayData)
    {
        foreach ($arrayData as $value_first) {
            if (($value_first['id'] != '')) {
                $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm JOIN user u ON sm.userId = u.id WHERE sm.id=' . $value_first['id'];

                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(Db::FETCH_ASSOC);
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
                            ]
                        );
                        if ($groupColor instanceof Group && $groupColor->color !== null) {
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
                    $sql = 'SELECT from_item as id FROM `systemic_map_chain` WHERE to_item = ' . $id;
                    $connection = $this->db;
                    $data = $connection->query($sql);
                    $data->setFetchMode(Db::FETCH_ASSOC);
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        $creatorInfo = null;
        if ($creator) {
            $creatorInfo = [$creatorId, $creator['account']->role];
        }
        if ($creator && $creatorInfo[1] !== AclRoles::MANAGER) {
            $response = [
                'code' => 0,
                'status' => 'You cannot delete this systemic item!',
            ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS (SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = ' . $data->systemicMapId . ' ';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();
            $out_nodes = [];
            $tree = [];

            $this->fillArray3($tree, $results_dist);


            $a = [
                'tree' => $tree,
            ];

            return $this->createArrayResponse($a, 'data');
        }
    }

    public function fillArray3(&$tree, $arrayData)
    {
        foreach ($arrayData as $value_first) {
            if (($value_first['id'] != '')) {
                $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm JOIN user u ON sm.userId = u.id WHERE sm.id=' . $value_first['id'];

                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(Db::FETCH_ASSOC);
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
                            ]
                        );
                        // var_dump($groupColorValue = $groupColor->color);die();
                        if ($groupColor instanceof Group && $groupColor->color !== null) {
                            $groupColorValue = $groupColor->color;
                        }

                        $groupTitle = $groupColor->title;
                    }
                    //  $color = array();
                    $item['color'] = $groupColorValue;
                    $item['groupTitle'] = $groupTitle;

                    //  array_push($item,$color);
                    $tree[] = $item;
                }
                foreach ($tree as &$item) {
                    $id = $item['id'];
                    $sql = 'SELECT from_item as id FROM `systemic_map_chain` WHERE to_item = ' . $id;
                    $connection = $this->db;
                    $data = $connection->query($sql);
                    $data->setFetchMode(Db::FETCH_ASSOC);
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $creator = static::getUserDetails($creatorId);
        $creatorInfo = null;
        if ($creator) {
            $creatorInfo = [$creatorId, $creator['account']->role];
        }
        if ($creator && $creatorInfo[1] !== AclRoles::MANAGER) {
            $response = [
                'code' => 0,
                'status' => 'You cannot delete this systemic item!',
            ];
        } else {
            $request = new Request();
            $data = $request->getJsonRawBody();
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS (SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = ' . $data->systemicMapId . ' ';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();
            $out_nodes = array();
            $tree = array();

            $this->fillArray3($tree, $results_dist);

            $action_grp_list = new ActionListGroup();
            $action_grp_list->systemic_map_id = $data->systemicMapId;
            $action_grp_list->title = $data->title;
            $action_grp_list->created_by = $creatorId;
            if (isset($data->description)) {
                $action_grp_list->description = $data->description;
            }
            if ($action_grp_list->save() === false) {
                $messagesErrors = array();
                foreach ($action_grp_list->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors,
                ];
            }
            $action_grp_list_id = $action_grp_list->getWriteConnection()->lastInsertId();

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
                    if (!$this->isArrayEmpty($last)) {
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

            }
            print_r($path);
            die();
            // $a = array(
            //   'tree' => $tree,
            // );

            //         return $this->createArrayResponse($a, 'data');
        }
    }

    public function maxDepth($arr)
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
            if ($string[$i] === '[') {
                ++$now;
                $max = $max < $now ? $now : $max;
            }

            if ($string[$i] === ']') {
                --$now;
            }
        }

        return $max;
    }

    public function deepness(array $arr)
    {
        $exploded = explode(',', json_encode($arr, JSON_FORCE_OBJECT) . "\n\n");
        $longest = 0;
        foreach ($exploded as $row) {
            $longest = (substr_count($row, ':') > $longest) ?
                substr_count($row, ':') : $longest;
        }

        return $longest;
    }

    public function isArrayEmpty($a)
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
        $validate = [
            'name' => ['mandatory' => true, 'regex' => null],
        ];

        $missing_input = [];

        foreach ($data as $key => $val) {
            $mandatory = $validate[$key] ?? false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
                'code' => 0,
                'status' => 'Required field: ' . implode(', ', $missing_input),
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $systemicStructureMap = new SystemicStructureMap();
        $systemicStructureMap->name = $data->name;
        $systemicStructureMap->by_whom = $creatorId;

        $systemicStructureMap->organization = $organization_id;
        $systemicStructureMap->lang = $data->lang;
        $systemicStructureMap->isActive = $data->isActive;
        $systemicStructureMap->processId = $data->processId;
        $systemicStructureMap->startDate = $data->startDate;
        $systemicStructureMap->endDate = $data->endDate;
        if ($systemicStructureMap->save() === false) {
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
                    1 => $creatorId,
                ],
            ]);

        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicStructureMap = SystemicStructureMap::findFirst(
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
        if ($systemicStructureMap instanceof SystemicStructureMap) {
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }

        $request = new Request();
        $data = $request->getJsonRawBody();

        $validate = array(
            'systemic_map_id' => array('mandatory' => true, 'regex' => null),
            'question' => array('mandatory' => true, 'regex' => null),
        );

        $missing_input = array();

        foreach ($data as $key => $val) {
            $mandatory = $validate[$key] ?? false;
            if ($mandatory && !trim($val)) {
                $missing_input[] = $key;
            }
        }

        if (!empty($missing_input)) {
            $response = [
                'code' => 0,
                'status' => 'Required field: ' . implode(', ', $missing_input),
            ];

            return $this->createArrayResponse($response, 'data');
        }

        if ($data->proposal === '') {
            $dp = '-';
        } else {
            $dp = $data->proposal;
        }

        $systemicStructureItem = new SystemicStructureMapItems();
        $systemicStructureItem->systemic_map_id = $data->systemic_map_id;
        $systemicStructureItem->question = $data->question;
        $systemicStructureItem->proposal = $dp;
        $systemicStructureItem->groupId = $data->groupId;
        $systemicStructureItem->itemType = $data->itemType;
        $systemicStructureItem->userId = $creatorId;
        if ($systemicStructureItem->save() === false) {
            $messagesErrors = array();
            foreach ($systemicStructureItem->getMessages() as $message) {
                //  print_r($message);
                $messagesErrors[] = $message;
            }
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

//            $systemicStructureMapItemId = (int)($systemicStructureItem->id);
//            $response = [
//                'code' => 1,
//                'status' => 'Success',
//                'data' => array('systemicStructureMapItemId' => $systemicStructureMapItemId),
//            ];
        }

        return $this->createArrayResponse($response, 'data');
    }


    public function deleteSystemicStructureItem($id)
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
                    1 => $creatorId,
                ],
            ]
        );
        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            /** @var Simple $systemicChains */
            $systemicChains = SystemicStructureMapChain::find(
                [
                    'conditions' => 'from_item =?1 OR to_item =?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );
            /** @var SystemicStructureMapChain $systemicChain */
            foreach ($systemicChains as $systemicChain) {
                $systemicChain->delete();
            }
            /** @var Simple $systemicItems */
            $systemicItems = SystemicStructureMapItems::find(
                [
                    'conditions' => 'id =?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );
            /** @var SystemicStructureMapItems $systemicItem */
            foreach ($systemicItems as $systemicItem) {
                $systemicItem->delete();
            }

            $response = [
                'code' => 1,
                'status' => 'Success!',
            ];
        } else {
            /** @var Simple $systemicItems */
            $systemicItems = SystemicStructureMapItems::find(
                [
                    'conditions' => 'userId =?1 AND id =?2',
                    'bind' => [
                        1 => $creatorId,
                        2 => $id,
                    ],
                ]
            );
            if ($systemicItems->count() > 0) {
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                ];
                /** @var SystemicStructureMapItems $systemicItem */
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

    public function getSystemicStructureItem($id, $type)
    {
        /** @var Simple $systemicMaps */
        $systemicMaps = SystemicStructureMapItems::find(
            [
                'conditions' => 'systemic_map_id = ?1 and itemType = ?2 ',
                'bind' => [
                    1 => $id,
                    2 => $type
                ],
            ]
        );
        $systemicMapsArray = [];
        $linksArray = [];
        if ($systemicMaps->count() > 0) {
            /** @var SystemicStructureMapItems $systemicMap */
            foreach ($systemicMaps as $systemicMap) {
                $groupColorValue = null;
                if ($systemicMap->groupId) {
                    $groupColor = Group::findFirst(
                        [
                            'conditions' => 'id = ?1',
                            'bind' => [
                                1 => $systemicMap->groupId,
                            ],
                        ]
                    );
                    // var_dump($groupColorValue = $groupColor->color);die();
                    if ($groupColor instanceof  Group && $groupColor->color !== null) {
                        $groupColorValue = $groupColor->color;
                    }
                }

                $systemicMapsArray[] = [
                    'id' => (int) $systemicMap->id,
                    'systemic_map_id' => (int) $systemicMap->systemic_map_id,
                    'name' => $systemicMap->question,
                    'proposal' => $systemicMap->proposal,
                    'group' => (int) $systemicMap->groupId,
                    'groupColor' => $groupColorValue,
                ];

                /** @var Simple $chains */
                $chains = SystemicStructureMapChain::find(
                    [
                        'conditions' => 'to_item =?1',
                        'bind' => [
                            1 => $systemicMap->id,
                        ],
                    ]
                );
                /** @var SystemicStructureMapChain $chain */
                foreach ($chains as $chain) {
                    $linksArray[] = array(
                        'source' => $this->findItemIndexForId($systemicMapsArray, (int) $chain->from_item),
                        'target' => $this->findItemIndexForId($systemicMapsArray, (int) $chain->to_item),
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
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
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
                    1 => $creatorId,
                ],
            ]
        );

        if ($user instanceof  User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicItems = SystemicStructureMapItems::findFirst(
                [
                    'conditions' => 'id = ?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );

            if ($systemicItems instanceof SystemicStructureMapItems) {
                if ($creatorId !== $systemicItems->userId) {
                    $organizationChecked = UserOrganization::findFirst(
                        [
                            'conditions' => 'user_id = ?1 AND organization_id = ?2',
                            'bind' => [
                                1 => $creatorId,
                                2 => $organization_id,
                            ],
                        ]
                    );

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
                        2 => $creatorId,
                    ],
                ]
            );
        }
        if ($systemicItems instanceof SystemicStructureMapItems) {
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

    public function getStructureChain($id, $itemType)
    {
        $connection = $this->db;
        $sql_dist = 'SELECT DISTINCT SC.id,SC.from_item,SC.to_item,I.itemType FROM `systemic_structure_map_chain` SC left JOIN systemic_map_structure_items I ON SC.from_item = I.id OR SC.to_item = I.id WHERE I.itemType = "' . $itemType . '" AND systemic_map_id = ' . $id . ' ';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();
        $resultArray = array();
        foreach ($results_dist as $item) {
            $resultArray[] = array(
                'id' => (int)$item['id'],
                'from_item' => (int)$item['from_item'],
                'to_item' => (int)$item['to_item'],
                'itemType' => $item['itemType']
            );
        }

        return $this->createArrayResponse($resultArray, 'chain');
    }


    public function deleteStructureChain($id)
    {
        $chain = SystemicStructureMapChain::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $id,
                ],
            ]
        );
        $chain->delete();

        $response = [
            'code' => 1,
            'status' => 'Success',
        ];
        return $this->createArrayResponse($response, 'data');
    }


    public function createStructureChain()
    {
        $request = new Request();
        $data = $request->getJsonRawBody();
        $creatorId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session ? $session->getIdentity() : null;
        }
        $creator = static::getUserDetails($creatorId);
        if ($creator && $creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $organization_id = $creator['organization']->organization_id;

        if (isset($data->from_item)) {
            $systemicMapsItems = SystemicStructureMapItems::findFirst(
                [
                    'conditions' => 'id = ?1 ',
                    'bind' => [
                        1 => $data->from_item
                    ],
                ]
            );

            if (!($systemicMapsItems instanceof SystemicStructureMapItems)) {
                $response = [
                    'code' => 0,
                    'status' => 'Cannot find item id: ' . $data->from_item,
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

        if (!($systemicMapsItems2 instanceof SystemicStructureMapItems)) {
            $response = [
                'code' => 0,
                'status' => 'Cannot find item id: ' . $data->to_item,
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $chain = new SystemicStructureMapChain();
        if (isset($data->from_item)) {
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
