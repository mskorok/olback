<?php

namespace App\Controllers;

use App\Constants\Services;
use App\Model\Process;
use App\Traits\Auth;
use App\Traits\Reports;
use App\Traits\Stats;
use PhalconRest\Mvc\Controllers\CollectionController;

class ReportController extends CollectionController
{

    use Stats, Reports, Auth;

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function singleReport($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [
                    'report' => $this->getSingleReport($process)
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
     * @return mixed
     * @throws \RuntimeException
     */
    public function groupReport($id)
    {
        $process = Process::findFirst((int) $id);
        if ($process instanceof Process) {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [
                    'report' => $this->getGroupReport($process)
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
     * @throws \RuntimeException
     */
    public function renderGroupReport($id): string
    {
        $process = Process::findFirst((int) $id);
        $data = $this->getGroupReportData($process);
        return $this->createArrayResponse($data, 'data');
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/group', ['data' => $data]);
    }


    /**
     * @param $id
     * @return string
     * @throws \RuntimeException
     */
    public function renderSingleGroup($id): string
    {
        $process = Process::findFirst((int) $id);
        $data = $this->getSingleReportData($process);
        return $this->createArrayResponse($data, 'data');
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/single', ['data' => $data]);
    }
}
