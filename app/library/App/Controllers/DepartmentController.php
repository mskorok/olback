<?php

namespace App\Controllers;

use App\Model\Organization;
use App\Model\ProcessDepartments;
use App\Traits\Auth;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\Department;
use App\Model\UserDepartment;
use Phalcon\Http\Request;

class DepartmentController extends CrudResourceController
{
    use Auth;

    /**
     * @return mixed
     */
    public function createDepartment()
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
        $organization = $creator['organization']->organization_id;
        $request = new Request();
        $data = $request->getJsonRawBody();
        $department = new Department();
        $department->title = $data->title;
        $department->description = $data->description;
        $department->organization_id = $organization;
        if ($department->save() === false) {
            $messagesErrors = array();
            foreach ($department->getMessages() as $message) {
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $departmentId = $department->getWriteConnection()->lastInsertId();
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => array('departmentId' => $departmentId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function getDepartment()
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
        $organization = $creator['organization']->organization_id;
        /** @var Simple $departments */
        $departments = Department::find(
            [
                'conditions' => '	organization_id = ?1',
                'bind' => [
                    1 => $organization,
                ],
            ]
        );

        $deps = [];
        /** @var Department $d */
        foreach ($departments as $d) {
            $deps[] = [
                'id' => (int)$d->id,
                'title' => $d->title,
                'description' => $d->description,
                'organization_id' => $d->organization_id
            ];
        }

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $deps,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function updateDepartment($id)
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
        $creator = static::getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;
        if ($creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }


        /** @var Department $department */
        $department = Department::findFirst(
            [
                'conditions' => 'id = ?1 AND organization_id = ?2',
                'bind' => [
                    1 => $id,
                    2 => $organization,
                ],
            ]
        );

        if ($department instanceof Department) {
            if (isset($data->title)) {
                $department->title = $data->title;
            }
            if (isset($data->description)) {
                $department->description = $data->description;
            }
            if ($department->save() === false) {
                $messagesErrors = array();
                foreach ($department->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors,
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
                'status' => 'You cannot edit this department!',
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function assignUserDepartment($userId)
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

        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );
        if ($user && property_exists('departments', $data)) {
            $items = $data->departments;
            if (\is_array($items)  || $items instanceof \Traversable) {
                foreach ($items as $departmentId) {
                    $department = new UserDepartment();
                    $department->user_id = $userId;
                    $department->department_id = $departmentId;
                    if ($department->save() === false) {
                        $messagesErrors = array();
                        foreach ($department->getMessages() as $message) {
                            $messagesErrors[] = $message;
                        }
                        $response = [
                            'code' => 0,
                            'status' => 'Error',
                            'data' => $messagesErrors,
                        ];
                        return $this->createArrayResponse($response, 'data');
                    }
                }
            }

            $response = [
                'code' => 1,
                'status' => 'Success'
            ];
        } else {
            $response = [
                'code' => 0,
                'status' => 'User does not exists'
            ];
        }


        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $departmentId
     * @return mixed
     */
    public function deleteDepartment($departmentId)
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
        $organization = $creator['organization']->organization_id;

        //check if user is authorized to delete the department
        $department = Department::findFirst(
            [
                'conditions' => 'id = ?1 AND organization_id = ?2',
                'bind' => [
                    1 => $departmentId,
                    2 => $organization,
                ],
            ]
        );

        if ($department instanceof Department) {
            $userDepartment = UserDepartment::findFirst(
                [
                    'conditions' => 'department_id = ?1',
                    'bind' => [
                        1 => $departmentId,
                    ],
                ]
            );
            if ($userDepartment instanceof UserDepartment) {
                $response = [
                    'code' => 0,
                    'status' => 'Assigned users'
                ];
                return $this->createArrayResponse($response, 'data');
            }

            $processDepartment = ProcessDepartments::findFirst(
                [
                    'conditions' => 'departmentId = ?1',
                    'bind' => [
                        1 => $departmentId,
                    ],
                ]
            );
            if ($processDepartment instanceof ProcessDepartments) {
                $response = [
                    'code' => 0,
                    'status' => 'Assigned processes'
                ];
                return $this->createArrayResponse($response, 'data');
            }

            $department->delete();
            $response = [
                'code' => 1,
                'status' => 'Success',
            ];
        } else {
            $response = [
                'code' => 0,
                'status' => 'You are not authorized to delete the department'
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function getUserDepartments($userId)
    {
        /** @var Simple $userDepartments */
        $userDepartments = UserDepartment::find(
            [
                'conditions' => 'user_id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );

        $departmentIds = [];
        /** @var UserDepartment $d */
        foreach ($userDepartments as $d) {
            $departmentIds[] = (int)$d->department_id;
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $departmentIds
        ];
        return $this->createArrayResponse($response, 'data');
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function updateUserDepartments($userId)
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

        //check for user
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );
        if ($user instanceof User) {
            /** @var Simple $userDepartments */
            $userDepartments = UserDepartment::find(
                [
                    'conditions' => 'user_id = ?1',
                    'bind' => [
                        1 => $userId,
                    ],
                ]
            );

            /** @var UserDepartment $d */
            foreach ($userDepartments as $d) {
                $d->delete();
            }

            if (property_exists('departments', $data)) {
                $items = $data->departments;
                if (\is_array($items)  || $items instanceof \Traversable) {
                    foreach ($items as $departmentId) {
                        $department = new UserDepartment();
                        $department->user_id = $userId;
                        $department->department_id = $departmentId;
                        if ($department->save() === false) {
                            $messagesErrors = array();
                            foreach ($department->getMessages() as $message) {
                                $messagesErrors[] = $message;
                            }
                            $response = [
                                'code' => 0,
                                'status' => 'Error',
                                'data' => $messagesErrors,
                            ];
                            return $this->createArrayResponse($response, 'data');
                        }
                    }
                }
            }

            $response = [
                'code' => 1,
                'status' => 'Success'
            ];
        } else {
            $response = [
                'code' => 0,
                'status' => 'You are not authorized to update departments'
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }
}
