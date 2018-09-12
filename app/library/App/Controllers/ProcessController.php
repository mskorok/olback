<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 11.09.18
 * Time: 19:31
 */

namespace App\Controllers;

use App\Model\Process;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class ProcessController extends CrudResourceController
{
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
}
