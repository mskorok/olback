<?php

namespace App\Controllers;

use App\Constants\Services;
use App\Model\Process;
use App\Model\SystemicStructureMap;
use App\Model\SystemicStructureMapChain;
use App\Model\SystemicStructureMapItems;
use App\Traits\Auth;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\SystemicMap;
use App\Model\SystemicMapItems;
use App\Model\SystemicMapChain;
use App\Model\ActionListGroup;
use App\Constants\AclRoles;
use App\Model\Group;

class SystemicMapControllerV2 extends CrudResourceController
{
    use Auth;

    protected $html;

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function getSystemicMap()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function getSystemicStructureMap()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function getSystemicMapByProcess($id)
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function getSystemicStructureMapByProcess($id)
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
                    'department' => $systemicMap->department,
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

    /**
     * @param $id
     * @return mixed
     */
    public function getSystemicItem($id)
    {
        /** @var Simple $systemicMapItems */
        $systemicMapItems = SystemicMapItems::find(
            [
                'conditions' => '	systemic_map_id = ?1',
                'bind' => [
                    1 => $id,
                ],
            ]
        );
        $systemicMapsArray = [];
        $linksArray = [];
        $i = 0;
        $map = [];
        if ($systemicMapItems->count() > 0) {
            /** @var SystemicMapItems $systemicMapItem */
            foreach ($systemicMapItems as $systemicMapItem) {
                $groupColorValue = null;
                if ($systemicMapItem->groupId !== null) {
                    $groupColor = Group::findFirst($systemicMapItem->groupId);

                    if ($groupColor instanceof Group && $groupColor->color !== null) {
                        $groupColorValue = $groupColor->color;
                    }
                }

                /** @var Simple $chains */
                $chains = SystemicMapChain::find(
                    [
                        'conditions' => 'to_item =?1',
                        'bind' => [
                            1 => $systemicMapItem->id,
                        ],
                    ]
                );

                $systemicMapsArray[] = [
                    'id' => $systemicMapItem->id,
                    'systemic_map_id' => $systemicMapItem->systemic_map_id,
                    'name' => $systemicMapItem->question,
                    'proposal' => $systemicMapItem->proposal,
                    'group' => (int)$systemicMapItem->groupId,
                    'groupColor' => $groupColorValue,
                    'count' => $i,
                    'chains' => $chains
                ];

                $map[$systemicMapItem->id] = $i;

                $i++;
            }


            foreach ($systemicMapsArray as $item) {
                /** @var array $chains */
                $chains = $item['chains'];
                /** @var SystemicMapChain $chain */
                foreach ($chains as $chain) {
                    if ($chain->from_item === null) {
                        $linksArray[] = [
                            'source' => 0,
                            'target' => 0,
                            'value' => 2,
                        ];
                    } else {
                        $linksArray[] = [
                            'source' => $map[$chain->from_item],
                            'target' => $map[$chain->to_item],
                            'value' => 2,
                        ];
                    }
                }
            }
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => ['nodes' => $systemicMapsArray, 'links' => $linksArray],
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @param $type
     * @return mixed
     */
    public function getSystemicStructureItem($id, $type)
    {
        /** @var Simple $systemicMapItems */
        $systemicMapItems = SystemicStructureMapItems::find(
            [
                'conditions' => 'systemic_map_id = ?1 and itemType = ?2 ',
                'bind' => [
                    1 => $id,
                    2 => $type
                ],
            ]
        );
        $systemicMapItemsArray = [];
        $linksArray = [];
        if ($systemicMapItems->count() > 0) {
            /** @var SystemicStructureMapItems $systemicMapItem */
            foreach ($systemicMapItems as $systemicMapItem) {
                $groupColorValue = null;
                if (null !== $systemicMapItem->groupId) {
                    $groupColor = Group::findFirst($systemicMapItem->groupId);

                    if ($groupColor instanceof Group && $groupColor->color !== null) {
                        $groupColorValue = $groupColor->color;
                    }
                }

                $systemicMapItemsArray[] = [
                    'id' => (int)$systemicMapItem->id,
                    'systemic_map_id' => (int)$systemicMapItem->systemic_map_id,
                    'name' => $systemicMapItem->question,
                    'proposal' => $systemicMapItem->proposal,
                    'group' => (int)$systemicMapItem->groupId,
                    'groupColor' => $groupColorValue,
                ];

                /** @var Simple $chains */
                $chains = SystemicStructureMapChain::find(
                    [
                        'conditions' => 'to_item =?1',
                        'bind' => [
                            1 => $systemicMapItem->id,
                        ],
                    ]
                );
                /** @var SystemicStructureMapChain $chain */
                foreach ($chains as $chain) {
                    $linksArray[] = [
                        'source' => $this->findItemIndexForId($systemicMapItemsArray, (int)$chain->from_item),
                        'target' => $this->findItemIndexForId($systemicMapItemsArray, (int)$chain->to_item),
                        'value' => 2,
                    ];
                }
            }
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => ['nodes' => $systemicMapItemsArray, 'links' => $linksArray],
        ];

        return $this->createArrayResponse($response, 'data');
    }


//    public function getSystemicItemOld($id)
//    {
//        //echo $id;die();
//        $systemicMaps = SystemicMapItems::find(
//            [
//                'conditions' => '     systemic_map_id = ?1',
//                'bind' => [
//                    1 => $id,
//                ],
//            ]
//        );
//        $systemicMapsArray = array();
//        $linksArray = array();
//        if ($systemicMaps) {
//            foreach ($systemicMaps as $systemicMap) {
//                $groupColorValue = null;
//                if (isset($systemicMap->groupId)) {
//                    $groupColor = Group::findFirst(
//                        [
//                            'conditions' => 'id = ?1',
//                            'bind' => [
//                                1 => $systemicMap->groupId,
//                            ],
//                        ]
//                    );
//                    // var_dump($groupColorValue = $groupColor->color);die();
//                    if ($groupColor->color != null) {
//                        $groupColorValue = $groupColor->color;
//                    }
//                }
//
//                $systemicMapsArray[] = array(
//                    'id' => $systemicMap->id,
//                    'systemic_map_id' => $systemicMap->systemic_map_id,
//                    'name' => $systemicMap->question,
//                    'proposal' => $systemicMap->proposal,
//                    'group' => intval($systemicMap->groupId),
//                    'groupColor' => $groupColorValue,
//                );
//
//                $chains = SystemicMapChain::find(
//                    [
//                        'conditions' => 'to_item =?1',
//                        'bind' => [
//                            1 => $systemicMap->id,
//                        ],
//                    ]
//                );
//                // echo "dada";die();
//                foreach ($chains as $chain) {
//                    $linksArray[] = array(
//                        'source' => $this->findItemIndexForId($systemicMapsArray, intval($chain->from_item)),
//                        'target' => $this->findItemIndexForId($systemicMapsArray, intval($chain->to_item)),
//                        'value' => 2,
//                    );
//                }
//            }
//        }
//        $response = [
//            'code' => 1,
//            'status' => 'Success',
//            'data' => array('nodes' => $systemicMapsArray, 'links' => $linksArray),
//        ];
//
//        return $this->createArrayResponse($response, 'data');
//    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function createSystemicMap()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

        $data = $this->request->getJsonRawBody();

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
        if (property_exists($data, 'processId')) {
            $process = Process::findFirst((int)$data->processId);
            if (!($process instanceof Process)) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => 'Process not found',
                ];
                return $this->createArrayResponse($response, 'data');
            }
        } else {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'Required field processId missed',
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

        $systemicMap->organization = $process->organizationId;
        $systemicMap->lang = $data->lang;
        $systemicMap->isActive = $data->isActive;
        $systemicMap->creator_id = $creatorId;
        $systemicMap->processId = $process->id;
        if ($systemicMap->save() === false) {
            $messagesErrors = [];
            foreach ($systemicMap->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $systemicMap->refresh();
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => ['systemicMapId' => $systemicMap->id],
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function createSystemicMapItem()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
        }


        $data = $this->request->getJsonRawBody();

        $validate = [
            'systemic_map_id' => ['mandatory' => true, 'regex' => null],
            'question' => ['mandatory' => true, 'regex' => null],
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

        if ($data->proposal === '') {
            $dp = '-';
        } else {
            $dp = $data->proposal;
        }

        $config = $this->getDI()->get(Services::CONFIG);

        $systemicItem = new SystemicMapItems();
        $systemicItem->systemic_map_id = $data->systemic_map_id;
        $systemicItem->question = $data->question;
        $systemicItem->proposal = $dp;
        if (!isset($data->groupId) || empty($data->groupId)) {
            $data->groupId = $config->settings->default->group;
        }
        $systemicItem->groupId = (int) $data->groupId;
        $systemicItem->userId = $creatorId;
        if ($systemicItem->save() === false) {
            $messagesErrors = [];
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
                $messagesErrors = [];
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
                'data' => ['systemicMapItemId' => $systemicMapItemId],
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function updateSystemicMap($id)
    {
        $data = $this->request->getJsonRawBody();
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

        $user = User::findFirst($creatorId);


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

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function updateSystemicItem($id)
    {
        $data = $this->request->getJsonRawBody();
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
            $systemicItems = SystemicMapItems::findFirst((int) $id);

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

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function deleteSystemicMap($id)
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $creatorId,
                ],
            ]
        );
        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            /** @var Simple $systemicMap */
            $systemicMap = SystemicMap::findFirst((int) $id);

            if ($systemicMap instanceof SystemicMap) {
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
                    /** @var Simple $systemicChains */
                    $systemicChains = SystemicMapChain::find(
                        [
                            'conditions' => 'from_item =?1 OR to_item =?1',
                            'bind' => [
                                1 => $systemicItem->id,
                            ],
                        ]
                    );
                    /** @var SystemicMapChain $systemicChain */
                    foreach ($systemicChains as $systemicChain) {
                        $systemicChain->delete();
                    }
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

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function deleteSystemicItem($id)
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
        $user = User::findFirst($creatorId);

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
            $systemicItems = SystemicMapItems::find((int)$id);

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

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function getSystemicItemTree($id)
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
        }

        /** @var Simple $systemicMapsR */
        $systemicMapsR = SystemicMap::find((int) $id);

        $systemicR = '';
        if ($systemicMapsR->count() > 0) {
            /** @var SystemicMap $systemicMapR */
            foreach ($systemicMapsR as $systemicMapR) {
                $systemicR = $systemicMapR->name;
            }
        }

        $creator = static::getUserDetails($creatorId);
        $creatorInfo = [$creatorId, $creator['account']->role];
        $connection = $this->db;
        $sql_dist = 'SELECT smi.id,u.first_name,u.last_name FROM `systemic_map_items` smi '
            . 'JOIN user u ON smi.userId = u.id WHERE smi.id NOT IN '
            . '(SELECT distinct smc.from_item as id FROM `systemic_map_chain` smc '
            . 'WHERE smc.from_item IS NOT NULL ) AND smi.systemic_map_id=' . $id . ';';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();

        $non_ch = [];
        foreach ($results_dist as $key => $value) {
            $non_ch[] = $value['id'];
        }
        $sql = 'SELECT to_item as id FROM `systemic_map_chain` '
            . 'WHERE from_item IS NULL AND to_item IN '
            . '(SELECT id FROM systemic_map_items WHERE systemic_map_id=' . $id . ')';

        $data = $connection->query($sql);
        $data->setFetchMode(Db::FETCH_ASSOC);
        $results = $data->fetchAll();
        $tree = $this->fillArray($results);
        if (\count($tree) === 0) {
            $a = [
                'tree' => $tree,
                'htmlCode' => '',
                'systemic_map_title' => $systemicR,
            ];

            return $this->createArrayResponse($a, 'data');
        }

        $this->html = '

        <li class="dd-item dd3-item item' . $tree[0]['id'] . " generals-item \" style='color:" . $tree[0]['color']
            . ";'  data-id=\"" . $tree[0]['id'] . '">
                  <div class="dd3-content" >
<div class="itemscolor" style="background-color:' . $this->colorLuminance($tree[0]['color'], 0.1) . '"></div>
                    ' . $tree[0]['question'] . '

                    <span class="pull-right">


                    <a class="fa fa-lg fa-plus" data-toggle="modal" data-target="#myModal' . $tree[0]['id'] . 'C"></a>
                    <a class="fa fa-lg fa-pencil-square-o" data-toggle="modal" data-target="#myModal' . $tree[0]['id']
            . 'E"></a>


                    </span>
                  </div>';
        $html = $this->arrayDepth($tree[0]['items'], $creatorInfo, $non_ch)['html'];
        $html .= '
				<data-sys-map-items-add lolo="myModal" add-func="addSysMapItem(' . $tree[0]['id']
            . ',question,proposal,group,color)" datasp="' . $tree[0]['id'] . '"></data-sys-map-items-add>

				<data-sys-map-items-edit lolo="myModal" edit-func="editSysMapItem(' . $tree[0]['id']
            . ',question,proposal,group,color)" datasp="' . $tree[0]['id'] . '" dataprop="' . $tree[0]['proposal']
            . '" dataque="' . $tree[0]['question'] . '" datagrp="' . $tree[0]['groupId'] . '" dataclr="'
            . $tree[0]['color'] . '"></data-sys-map-items-edit>

</li>
';
        $a = [
            'tree' => $tree,
            'htmlCode' => $html,
            'systemic_map_title' => $systemicR,
        ];

        return $this->createArrayResponse($a, 'data');
    }

    /**
     * @param array $arrayData
     * @return array
     */
    public function fillArray(array $arrayData): array
    {
        $tree = [];
        foreach ($arrayData as $value) {
            $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm '
                . 'JOIN user u ON sm.userId = u.id WHERE sm.id=' . $value['id'];

            $connection = $this->db;
            $data = $connection->query($sql);
            $data->setFetchMode(Db::FETCH_ASSOC);
            $iresults = $data->fetchAll();
            foreach ($iresults as $item) {
                $groupTitle = '';
                $groupColorValue = null;
                if (isset($item['groupId'])) {
                    $groupColor = Group::findFirst((int) $item['groupId']);
                    if ($groupColor instanceof Group && $groupColor->color !== null) {
                        $groupColorValue = $groupColor->color;
                    }

                    $groupTitle = $groupColor->title;
                }
                $item['color'] = $groupColorValue;
                $item['groupTitle'] = $groupTitle;

                $tree[] = $item;
            }
        }
        $treeArray = [];
        foreach ($tree as $item) {
            $id = $item['id'];
            $sql = 'SELECT to_item as id FROM `systemic_map_chain` WHERE from_item = ' . $id;
            $connection = $this->db;
            $data = $connection->query($sql);
            $data->setFetchMode(Db::FETCH_ASSOC);
            $ids = $data->fetchAll();
            $item['items'] = $this->fillArray($ids);
            $treeArray[] = $item;
        }
        $tree = $treeArray;
        return $tree;
    }

    /**
     * @param array $array
     * @param $creatorInfo
     * @param $non_ch
     * @return array
     */
    public function arrayDepth(array $array, $creatorInfo, $non_ch): array
    {
        $max_depth = 1;

        foreach ($array as $value) {
            if (\is_array($value)) {
                if (isset($value['id'])) {
                    if ((AclRoles::ADMINISTRATOR === $creatorInfo[1]) || (AclRoles::MANAGER === $creatorInfo[1])) {
                        $delete_raw = '<a class="fa fa-lg fa-trash-o" data-ng-click="deleteSysMapItem('
                            . $value['id'] . ')"></a>';
                    } else {
                        if ((int)$creatorInfo[0] === (int)$value['userId']) {
                            $delete_raw = '<a class="fa fa-lg fa-trash-o" data-ng-click="deleteSysMapItem('
                                . $value['id'] . ')"></a>';
                        } else {
                            $delete_raw = '';
                        }
                    }

                    if (!\in_array($value['id'], $non_ch, true)) {
                        $delete_raw = '';
                    }
                    $this->html .= '<ol class="dd-list"><li class="dd-item dd3-item item' . $value['id']
                        . " generals-item\" style=\“color:" . $value['color'] . ";\” data-id=\"" . $value['id']
                        . '"><div class="dd3-content" ><div class="itemscolor" style="background-color:'
                        . $this->colorLuminance($value['color'], 0.1)
                        . '"></div>' . $value['question'] . '<span class="pull-right">'
                        . $delete_raw . '<a class="fa fa-lg fa-plus" data-toggle="modal" data-target="#myModal'
                        . $value['id']
                        . 'C"></a><a class="fa fa-lg fa-pencil-square-o" data-toggle="modal" data-target="#myModal'
                        . $value['id'] . 'E"></a></span><data-sys-map-items-add lolo="myModal" add-func="addSysMapItem('
                        . $value['id'] . ',question,proposal,group,color)" datasp="'
                        . $value['id']
                        . '"></data-sys-map-items-add><data-sys-map-items-edit lolo="myModal"'
                        . ' edit-func="editSysMapItem('
                        . $value['id'] . ',question,proposal,group,color)" datasp="'
                        . $value['id'] . '" dataprop="' . $value['proposal'] . '" dataque="'
                        . $value['question'] . '" datagrp="' . $value['groupId']
                        . '" dataclr="' . $value['color']
                        . '"></data-sys-map-items-edit>'
                        . '<div style="color: #3276b1;font-size: 12px;" class="item-infos "><strong>by: </strong>'
                        . $value['first_name'] . ' ' . $value['last_name']
                        . '</div><div class="item-groupname">' . $value['groupTitle'] . '</div></div>';
                }

                $depth = $this->arrayDepth($value['items'], $creatorInfo, $non_ch)['max'] + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }

                $this->html .= '</li></ol>';
            }
        }

        return ['max' => $max_depth, 'html' => $this->html];
    }

    protected function colorLuminance($hex, $percent = null)
    {
        if (!$percent) {
            return $hex;
            //todo
        }
        return $hex;//todo
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function createActionListGroup()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
            $data = $this->request->getJsonRawBody();
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM '
                . 'JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS '
                . '(SELECT * FROM systemic_map_chain sm2 '
                . 'WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = ' . $data->systemicMapId . ';';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();

            $tree = $this->fillArray2($results_dist);

            $a = [
                'tree' => $tree,
            ];

            return $this->createArrayResponse($a, 'data');
        }
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param array $arrayData
     * @return array
     */
    protected function fillArray2(array $arrayData): array
    {
        $tree = [];
        foreach ($arrayData as $value) {
            if ($value['id'] !== '') {
                $sql = 'SELECT sm.*,u.first_name,u.last_name FROM systemic_map_items sm '
                    . 'JOIN user u ON sm.userId = u.id WHERE sm.id=' . $value['id'];

                $connection = $this->db;
                $data = $connection->query($sql);
                $data->setFetchMode(Db::FETCH_ASSOC);
                $iresults = $data->fetchAll();
                foreach ($iresults as $item) {
                    $groupTitle = '';
                    $groupColorValue = null;
                    if (isset($item['groupId'])) {
                        $groupColor = Group::findFirst((int) $item['groupId']);

                        if ($groupColor instanceof Group && $groupColor->color !== null) {
                            $groupColorValue = $groupColor->color;
                        }

                        $groupTitle = $groupColor->title;
                    }
                    $item['color'] = $groupColorValue;
                    $item['groupTitle'] = $groupTitle;
                    $tree[] = $item;
                }
            }
        }
        foreach ($tree as $item) {
            $id = $item['id'];
            $sql = 'SELECT from_item as id FROM `systemic_map_chain` WHERE to_item = ' . $id;
            $connection = $this->db;
            $data = $connection->query($sql);
            $data->setFetchMode(Db::FETCH_ASSOC);
            $ids = $data->fetchAll();
            $item['items'] = $this->fillArray2($ids);
        }
        return $tree;
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    protected function createActionListGroup4()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
            $data = $this->request->getJsonRawBody();
            $connection = $this->db;
            $sql_dist = 'SELECT SM.to_item as id FROM `systemic_map_chain` SM '
                . 'JOIN systemic_map_items SI ON SI.id = SM.to_item WHERE NOT EXISTS '
                . '(SELECT * FROM systemic_map_chain sm2 WHERE sm2.from_item = SM.to_item) AND SI.systemic_map_id = '
                . $data->systemicMapId . ';';
            $data_dist = $connection->query($sql_dist);
            $data_dist->setFetchMode(Db::FETCH_ASSOC);
            $results_dist = $data_dist->fetchAll();

            $tree = $this->fillArray2($results_dist);

            $list = new ActionListGroup();
            $list->systemic_map_id = $data->systemicMapId;
            $list->title = $data->title;
            $list->created_by = $creatorId;
            if (isset($data->description)) {
                $list->description = $data->description;
            }
            if ($list->save() === false) {
                $messagesErrors = [];
                foreach ($list->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors,
                ];
                return $this->createArrayResponse($response, 'data');
            }

            $list->refresh();
            foreach ($tree as $key => $value) {//TODO
                $length = $this->deepness($value);
                $path = [];
                $last = $value['items'];
                $path[] = [
                    'priority' => -1,
                    'data' => $last,
                ];
                for ($i = 0; $i < $length; ++$i) {
                    if (!$this->isArrayEmpty($last)) {
                        $path[] = [
                            'priority' => $i,
                            'data' => $last,
                        ];
                        $last = $last['items'];
                    } else {
                        break;
                    }
                }
            }
            $a = [
                'tree' => $tree,
            ];

            return $this->createArrayResponse($a, 'data');
        }
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $arr
     * @return int
     */
    public function maxDepth($arr): int
    {

        // json encode
        $string = json_encode($arr);
        // removing string values to avoid braces in strings
        $string = preg_replace('/\"(.*?)\"/', '""', $string);
        //Replacing object braces with array braces
        $string = str_replace(['{', '}'], ['[', ']'], $string);

        $length = \strlen($string);
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

    /**
     * @param array $arr
     * @return int
     */
    public function deepness(array $arr): int
    {
        $exploded = explode(',', json_encode($arr, JSON_FORCE_OBJECT) . "\n\n");
        $longest = 0;
        foreach ($exploded as $row) {
            $longest = (substr_count($row, ':') > $longest) ?
                substr_count($row, ':') : $longest;
        }

        return $longest;
    }

    /**
     * @param array $a
     * @return bool
     */
    public function isArrayEmpty(array $a): bool
    {
        foreach ($a as $elm) {
            if (!empty($elm)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function createSystemicStructureMap()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

        $data = $this->request->getJsonRawBody();

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
        if (property_exists($data, 'processId')) {
            $process = Process::findFirst((int)$data->processId);
            if (!($process instanceof Process)) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => 'Process not found',
                ];
                return $this->createArrayResponse($response, 'data');
            }
        } else {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'Required field processId missed',
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $systemicStructureMap = new SystemicStructureMap();
        $systemicStructureMap->name = $data->name;
        $systemicStructureMap->creator_id = $creatorId;

        $systemicStructureMap->organization = $process->organizationId;
        $systemicStructureMap->lang = $data->lang;
        $systemicStructureMap->isActive = $data->isActive;
        $systemicStructureMap->processId = $process->id;
        $systemicStructureMap->startDate = $data->startDate;
        $systemicStructureMap->endDate = $data->endDate;
        if ($systemicStructureMap->save() === false) {
            $messagesErrors = [];
            foreach ($systemicStructureMap->getMessages() as $message) {
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
                'data' => ['systemicStructureMapId' => $systemicMapId],
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function updateSystemicStructureMap($id)
    {
        $data = $this->request->getJsonRawBody();
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

        $user = User::findFirst($creatorId);

        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicStructureMap = SystemicStructureMap::findFirst(
                [
                    'conditions' => 'id = ?1 AND organization = ?2',
                    'bind' => [
                        1 => $id,
                        2 => $organization_id,
                    ],
                ]
            );
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

    /**
     * @return mixed
     */
    public function createSystemicStructureMapItem()
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $data = $this->request->getJsonRawBody();

        $validate = [
            'systemic_map_id' => ['mandatory' => true, 'regex' => null],
            'question' => ['mandatory' => true, 'regex' => null],
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
            $messagesErrors = [];
            foreach ($systemicStructureItem->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $systemicStructureItem->refresh();
            $chain = new SystemicStructureMapChain();
            if (empty($data->from_item)) {
                $chain->from_item = null;
            } else {
                $chain->from_item = $data->from_item;
            }
            $chain->to_item = $systemicStructureItem->id;
            if ($chain->save() === false) {
                $messagesErrors = [];
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
                'data' => ['systemicStructureMapItemId' => $systemicStructureItem->id],
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function deleteSystemicStructureItem($id)
    {
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

        $user = User::findFirst($creatorId);

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
            /** @var SystemicStructureMapItems $systemicItem */
            $systemicItem = SystemicStructureMapItems::findFirst((int)$id);
            $systemicItem->delete();

            $response = [
                'code' => 1,
                'status' => 'Success!',
            ];
        } else {
            /** @var Simple $systemicItem */
            $systemicItem = SystemicStructureMapItems::findFirst(
                [
                    'conditions' => 'userId =?1 AND id =?2',
                    'bind' => [
                        1 => $creatorId,
                        2 => $id,
                    ],
                ]
            );
            if ($systemicItem instanceof SystemicStructureMapItems) {
                $systemicChain = SystemicStructureMapChain::find(
                    [
                        'conditions' => 'from_item =?1 OR to_item =?1',
                        'bind' => [
                            1 => $systemicItem->id,
                        ],
                    ]
                );
                if ($systemicChain instanceof SystemicStructureMapChain) {
                    $response = [
                        'code' => 0,
                        'status' => 'You cannot delete this systemic item!',
                    ];
                } else {
                    $response = [
                        'code' => 1,
                        'status' => 'Success!',
                    ];
                    $systemicItem->delete();
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



    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function updateSystemicStructureItem($id)
    {
        $data = $this->request->getJsonRawBody();
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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
        $organization_id = $creator['organization']->organization_id;//todo

        $user = User::findFirst($creatorId);

        if ($user instanceof User && (AclRoles::MANAGER === $user->role || AclRoles::ADMINISTRATOR === $user->role)) {
            $systemicItem = SystemicStructureMapItems::findFirst((int)$id);

            if ($systemicItem instanceof SystemicStructureMapItems && $creatorId !== $systemicItem->userId) {
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
        } else {
            $systemicItem = SystemicStructureMapItems::findFirst(
                [
                    'conditions' => 'id = ?1 AND userId = ?2',
                    'bind' => [
                        1 => $id,
                        2 => $creatorId,
                    ],
                ]
            );
        }
        if ($systemicItem instanceof SystemicStructureMapItems) {
            if (isset($data->question)) {
                $systemicItem->question = $data->question;
            }
            if (isset($data->proposal)) {
                $systemicItem->proposal = $data->proposal;
            }
            if (isset($data->groupId)) {
                $systemicItem->groupId = $data->groupId;
            }
            $systemicItem->save();
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

    /**
     * @param $id
     * @param $itemType
     * @return mixed
     */
    public function getStructureChain($id, $itemType)
    {
        $connection = $this->db;
        $sql_dist = 'SELECT DISTINCT SC.id,SC.from_item,SC.to_item,I.itemType '
            . 'FROM `systemic_structure_map_chain` SC '
            . 'LEFT JOIN systemic_map_structure_items I ON SC.from_item = I.id OR SC.to_item = I.id '
            . 'WHERE I.itemType = "' . $itemType . '" AND systemic_map_id = ' . $id . ' ';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(Db::FETCH_ASSOC);
        $results_dist = $data_dist->fetchAll();
        $resultArray = [];
        foreach ($results_dist as $item) {
            $resultArray[] = [
                'id' => (int)$item['id'],
                'from_item' => (int)$item['from_item'],
                'to_item' => (int)$item['to_item'],
                'itemType' => $item['itemType']
            ];
        }

        return $this->createArrayResponse($resultArray, 'chain');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteStructureChain($id)
    {
        $chain = SystemicStructureMapChain::findFirst((int)$id);
        $response = [
            'code' => 0,
            'status' => 'Error',
        ];
        if ($chain->delete()) {
            $response = [
                'code' => 1,
                'status' => 'Success',
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function createStructureChain()
    {
        $data = $this->request->getJsonRawBody();
        $creatorId = $this->getAuthenticatedId();
        if (null === $creatorId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
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

        if (isset($data->from_item) && is_numeric($data->from_item)) {
            $systemicMapsItemFrom = SystemicStructureMapItems::findFirst((int)$data->from_item);

            if (!($systemicMapsItemFrom instanceof SystemicStructureMapItems)) {
                $response = [
                    'code' => 0,
                    'status' => 'Cannot find item id: ' . $data->from_item,
                ];
                return $this->createArrayResponse($response, 'data');
            }
        }
        if (isset($data->to_item) && is_numeric($data->to_item)) {
            $systemicMapsItemTo = SystemicStructureMapItems::findFirst((int)$data->to_item);
            if (!($systemicMapsItemTo instanceof SystemicStructureMapItems)) {
                $response = [
                    'code' => 0,
                    'status' => 'Cannot find item id: ' . $data->to_item,
                ];
                return $this->createArrayResponse($response, 'data');
            }
        } else {
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

    /**
     * @param array $arr
     * @param $id
     * @return int
     */
    private function findItemIndexForId(array $arr, $id): int
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
}
