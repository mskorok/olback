<?php

namespace App\Controllers;

use App\Constants\Services;
use App\Model\Process;
use App\Traits\Stats;
use PhalconRest\Mvc\Controllers\CollectionController;

class ReportController extends CollectionController
{

    use Stats;

    public function singleReport($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [

                ],
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process not found!',
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function groupReport($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [

                ],
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Process not found!',
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return string
     */
    public function renderSingleReport($id): string
    {
        $process = Process::findFirst((int) $id);
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/single', ['process' => $process]);
    }


    /**
     * @param $id
     * @return string
     */
    public function renderSingleGroup($id): string
    {
        $process = Process::findFirst((int) $id);
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/single', ['process' => $process]);
    }
}
