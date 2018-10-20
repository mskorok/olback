<?php

namespace App\Controllers;

use App\Model\Group;
use App\Model\GroupTemplate;
use App\Model\User;
use App\Model\UserOrganization;
use App\Traits\Auth;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\Organization;

class OrganizationController extends CrudResourceController
{
    use Auth;

    /**
     * @return mixed
     *
     */
    public function getOrgs()
    {
        $userId = $this->getAuthenticatedId();
        if (null === $userId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
        }


        /** @var Simple $organizations */
        $organizations = Organization::find(
            [
                'conditions' => 'userId = ?1',
                'bind' => [
                    1 => $userId
                ]
            ]
        );
        $orgs = [];
        if ($organizations) {
            /** @var Organization $or */
            foreach ($organizations as $or) {
                $orgs[] = [
                    'id' => $or->id,
                    'name' => $or->name,
                    'description' => $or->description
                ];
            }
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $orgs
        ];

        return $this->createArrayResponse($response, 'data');
    }


    /**
     * @return mixed
     *
     */
    public function createOrg()
    {
        $data = $this->request->getJsonRawBody();

        $userId = $this->getAuthenticatedId();
        $messagesErrors = [];
        if ($userId !== null) {
            $organizationCheck = Organization::findFirst(
                [
                    'conditions' => 'userId = ?1',
                    'bind' => [
                        1 => $userId
                    ]
                ]
            );

            if ($organizationCheck instanceof Organization) {
                $userOrganization = UserOrganization::findFirst([
                    'conditions' => 'organization_id = ?1 AND user_id =?2',
                    'bind' => [
                        1 => $organizationCheck->id,
                        2 => $userId
                    ]
                ]);

                if (!($userOrganization instanceof UserOrganization)) {
                    $assignedOrg = new UserOrganization();
                    $assignedOrg->organization_id = $organizationCheck->id;
                    $assignedOrg->user_id = $userId;
                    if ($assignedOrg->save() === false) {
                        foreach ($assignedOrg->getMessages() as $message) {
                            $messagesErrors[] = $message;
                        }
                    }
                }
                if (\count($messagesErrors) > 0) {
                    $response = [
                        'code' => 0,
                        'status' => 'Error',
                        'data' => $messagesErrors
                    ];
                } else {
                    $response = [
                        'code' => 1,
                        'status' => 'Success',
                        'message' => 'Organization exist',
                        'data' =>[
                            'organizationId' => $organizationCheck->id
                        ]
                    ];
                }


                return $this->createArrayResponse($response, 'data');
            }
        } else {
            $organizationCheck = Organization::findFirst(
                [
                    'conditions' => 'name = ?1',
                    'bind' => [
                        1 => $data->name
                    ]
                ]
            );
            if ($organizationCheck instanceof Organization) {
                $userOrganization = UserOrganization::findFirst([
                    'conditions' => 'organization_id = ?1 AND user_id =?2',
                    'bind' => [
                        1 => $organizationCheck->id,
                        2 => $data->userId
                    ]
                ]);
                if (!($userOrganization instanceof UserOrganization)) {
                    $assignedOrg = new UserOrganization();
                    $assignedOrg->organization_id = $organizationCheck->id;
                    $assignedOrg->user_id = $data->userId;
                    if ($assignedOrg->save() === false) {
                        foreach ($assignedOrg->getMessages() as $message) {
                            $messagesErrors[] = $message;
                        }
                    }
                }
                if (\count($messagesErrors) > 0) {
                    $response = [
                        'code' => 0,
                        'status' => 'Error',
                        'data' => $messagesErrors
                    ];
                } else {
                    $response = [
                        'code' => 1,
                        'status' => 'Success',
                        'message' => 'Organization exist',
                        'data' =>[
                            'organizationId' => $organizationCheck->id
                        ]
                    ];
                }

                return $this->createArrayResponse($response, 'data');
            }
        }




        $organization = new Organization();
        $organization->name = $data->name;
        $organization->description = $data->description;
        $organization->userId = $userId ?: $data->userId;
        if ($organization->save() === false) {
            foreach ($organization->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'step' => 'Creation failed',
                'data' => serialize($organization->getMessages())
            ];
        } else {
            $organization->refresh();
            $assignedOrg = new UserOrganization();
            $assignedOrg->organization_id = $organization->id;
            $assignedOrg->user_id = $userId ?: $data->userId;
            if ($assignedOrg->save() === false) {
                foreach ($assignedOrg->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
            }


            $errors = $this->createDefaultGroups($organization);
            $messagesErrors = array_merge($messagesErrors, $errors);
            if (\count($messagesErrors) > 0) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors
                ];
            } else {
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'data' =>[
                        'organizationId' => $organization->id
                    ]
                ];
            }
        }
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function updateOrg()
    {
        $userId = $this->getAuthenticatedId();
        if (null === $userId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $data = $this->request->getJsonRawBody();

        $organization = Organization::findFirst(
            [
                'conditions' => 'userId = ?1',
                'bind' => [
                    1 => $userId
                ]
            ]
        );

        if ($organization instanceof Organization) {
            if (isset($data->name)) {
                $organization->name = $data->name;
            }

            if (isset($data->description)) {
                $organization->description = $data->description;
            }
            if ($organization->save() === false) {
                $messagesErrors = [];
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
            }
        } else {
            $response = [
                'code' => 0,
                'status' => 'Organization does not exist'
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param Organization $organization
     * @return array
     */
    protected function createDefaultGroups(Organization $organization): array
    {
        $messages = [];
        $user = User::findFirst($organization->userId);
        if ($user instanceof User) {
            /** @var Simple $templates */
            $templates = GroupTemplate::find();
            /** @var GroupTemplate $template */
            foreach ($templates as $template) {
                $group = new Group();
                $group->organization = $organization->id;
                $group->creatorId = $user->id;
                $group->title = $template->title;
                $group->color = $template->color;
                if ($group->save() === false) {
                    $msg = $group->getMessages();
                    foreach ($msg as $message) {
                        $messages[] = $message->getMessage();
                    }
                }
            }
        }

        return $messages;
    }
}
