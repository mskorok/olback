<?php

namespace App\Controllers;

use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\Organization;
use Phalcon\Http\Request;

class OrganizationController extends CrudResourceController
{


    public function getOrgs()
    {
        $userId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session ? $session->getIdentity() : null; // For example; 1
            // $user = \Users::findFirstById($userId);
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
                $orgs[] = array(
                    'id' => $or->id,
                    'name' => $or->name,
                    'description' => $or->description
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


    public function createOrg()
    {
        $request = new Request();
        $data = $request->getJsonRawBody();

        $userId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session ? $session->getIdentity() : null; // For example; 1
            // $user = \Users::findFirstById($userId);
        }

        $organizationCheck = Organization::findFirst(
            [
                'conditions' => 'userId = ?1',
                'bind' => [
                    1 => $userId
                ]
            ]
        );

        if ($organizationCheck) {
            $response = [
                'code' => 1,
                'status' => 'Cannot create organization'
            ];

            return $this->createArrayResponse($response, 'data');
        }


        //
        $organization = new Organization();
        $organization->name = $data->name;
        $organization->description = $data->description;
        $organization->userId = $userId;
        if ($organization->save() === false) {
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
        }
        return $this->createArrayResponse($response, 'data');
    }

    public function updateOrg()
    {
        $userId = null;
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $userId = $session ? $session->getIdentity() : null; // For example; 1
            // $user = \Users::findFirstById($userId);
        }

        $request = new Request();
        $data = $request->getJsonRawBody();

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
        } else {
            $response = [
                'code' => 0,
                'status' => 'Organization does not exist'
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }
}
