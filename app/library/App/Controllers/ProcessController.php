<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 11.09.18
 * Time: 19:31
 */

namespace App\Controllers;

use App\Model\Pis;
use App\Model\Process;
use App\Model\SystemicMap;
use App\Model\SystemicMapItems;
use App\Model\User;
use App\Traits\Auth;
use App\Traits\CheckSteps;
use App\Traits\Surveys;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class ProcessController extends CrudResourceController
{
    use Auth, CheckSteps, Surveys;

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function getProcessData($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $user = $this->getAuthenticated();
            if ($user instanceof User) {
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'process' => $process,
                    'steps' => $this->getCurrentStepPositions($process, $user),
                ];
                return $this->createArrayResponse($response, 'data');
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'User not authorized'
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process not found'
        ];
        return $this->createArrayResponse($response, 'data');
    }

    public function addCurrentReality($id)
    {
        $process = Process::findFirst((int)$id);
        if ($process instanceof Process) {
            $data = $this->request->getJsonRawBody();
            $process->CurrentReality = $data->text;
            if ($process->save()) {
                return $this->createOkResponse();
            }
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process with id='.$id.' not exist',
        ];
        return $this->createArrayResponse($response, 'data');
    }

    public function addSharedVision($id)
    {
        $process = Process::findFirst((int)$id);
        if ($process instanceof Process) {
            $data = $this->request->getJsonRawBody();
            $process->SharedVision = $data->text;
            if ($process->save()) {
                return $this->createOkResponse();
            }
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process with id='.$id.' not exist',
        ];
        return $this->createArrayResponse($response, 'data');
    }

    public function addInitialIntentions($id)
    {
        $process = Process::findFirst((int)$id);
        if ($process instanceof Process) {
            $data = $this->request->getJsonRawBody();
            $process->InitialIntentions = $data->text;
            if ($process->save()) {
                return $this->createOkResponse();
            }
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process with id='.$id.' not exist',
        ];
        return $this->createArrayResponse($response, 'data');
    }


    public function getActions($id)
    {
        $process = Process::findFirst((int)$id);
        if ($process instanceof Process) {
            /** @var Simple $maps */
            $maps = $process->getSystemicMap();
            if ($maps->count() > 0) {
                $items = [];
                $aar = [];
                /** @var SystemicMap $map */
                foreach ($maps as $map) {
                    /** @var Simple $mapItems */
                    $mapItems = $map->getSystemicMapItems();
                    /** @var SystemicMapItems $item */
                    foreach ($mapItems as $item) {
                        if ($item->survey === null) {
                            $items[] = $item;
                        } else {
                            $aar[] = $item->getSurvey();
                        }
                    }
                }

                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'data' => $items,
                    'aar' => $aar
                ];
                return $this->createArrayResponse($response, 'data');
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'Map not found'
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process not found'
        ];
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function checkStep($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $user = $this->getAuthenticated();
            if ($user instanceof User) {
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'data' => $this->getCurrentStepPositions($process, $user),
                ];
                return $this->createArrayResponse($response, 'data');
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'User not authorized'
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process not found'
        ];
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function createPIS($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $user = $this->getAuthenticated();
            if ($user instanceof User) {
                $pis = new Pis();
                $pis->process_id = $process->id;
                $pis->user_id = $user->id;
                if ($pis->save()) {
                    $response = [
                        'code' => 1,
                        'status' => 'Success',
                    ];
                    return $this->createArrayResponse($response, 'data');
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => 'PIS'
                ];
                return $this->createArrayResponse($response, 'data');
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'User not authorized'
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process not found'
        ];
        return $this->createArrayResponse($response, 'data');
    }

    /********************** CRUD METHODS ******************************************/

    protected function transformPostDataValue($key, $value, $data)
    {
        if ($key === 'creator_id') {
            $value = $this->getAuthenticatedId();
        }
        return $value;
    }
}
