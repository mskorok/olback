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
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/group', [
            'reportStartDate' => $data['reportStartDate'],
            'reportEndDate' => $data['reportEndDate'],
            'personName' => $data['personName'],
            'organizationName' => $data['organizationName'],
            'groups' => $data['groups'],
            'index' => $data['index'],
            'scoresGroupsArray' => $data['scoresGroupsArray'],
            'scoresOrderArray' => $data['scoresOrderArray'],
            'groupsGraph' => $data['groupsGraph'],
            'orderGraph' => $data['orderGraph']
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws \RuntimeException
     */
    public function renderSingleGroup($id)
    {
        $process = Process::findFirst((int) $id);
        $data = $this->getSingleReportData($process);
//        return $this->createArrayResponse($data['groupsGraph'], 'data');
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/single', [
            'reportStartDate' => $data['reportStartDate'],
            'reportEndDate' => $data['reportEndDate'],
            'personName' => $data['personName'],
            'countByRoles' => $data['countByRoles'],
            'organizationName' => $data['organizationName'],
            'groups' => $data['groups'],
            'index' => $data['index'],
            'scoresGroupsArray' => $data['scoresGroupsArray'],
            'scoresOrderArray' => $data['scoresOrderArray'],
            'groupsGraph' => $data['groupsGraph'],
            'orderGraph' => $data['orderGraph']
        ]);
    }
}
