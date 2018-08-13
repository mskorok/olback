<?php

namespace App\Controllers;

use App\Model\Group;
use App\Model\GroupTemplate;
use App\Model\User;
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
        if (null === $userId) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => ['User not authenticated']
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $organizationCheck = Organization::findFirst(
            [
                'conditions' => 'userId = ?1',
                'bind' => [
                    1 => $userId
                ]
            ]
        );

        if ($organizationCheck instanceof Organization) {
            $response = [
                'code' => 1,
                'status' => 'Cannot create organization'
            ];

            return $this->createArrayResponse($response, 'data');
        }


        $organization = new Organization();
        $organization->name = $data->name;
        $organization->description = $data->description;
        $organization->userId = $userId;
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
            $organization->refresh();
            $messagesErrors = $this->createDefaultGroups($organization);
            if (\count($messagesErrors) > 0) {
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
