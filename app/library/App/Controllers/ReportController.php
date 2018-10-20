<?php

namespace App\Controllers;

use App\Constants\Services;
use App\Model\GroupReport;
use App\Model\Process;
use App\Traits\Auth;
use App\Traits\Reports;
use App\Traits\Stats;
use PhalconRest\Mvc\Controllers\CollectionController;

class ReportController extends CollectionController
{

    use Stats, Reports, Auth;

    /**
     * @return mixed
     */
    public function singleReport()
    {
        try {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [
                    'report' => $this->getSingleReport()
                ],
            ];
            return $this->createArrayResponse($response, 'data');
        } catch (\RuntimeException $exception) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $exception->getMessage(),
            ];

            return $this->createArrayResponse($response, 'data');
        }
    }

    /**
     * @return mixed
     */
    public function groupReport()
    {
        try {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [
                    'report' => $this->getGroupReport()
                ],
            ];
            return $this->createArrayResponse($response, 'data');
        } catch (\RuntimeException $exception) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $exception->getMessage(),
            ];

            return $this->createArrayResponse($response, 'data');
        }
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function renderGroupReport(): string
    {
        $data = $this->getGroupReportData();
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        return $view->render('report/group', [
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


    /**
     * @return string
     * @throws \RuntimeException
     */
    public function renderSingleReport(): string
    {
        $data = $this->getSingleReportData();
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

    /**
     * @return mixed
     */
    public function singleReportCreate()
    {
        try {
            $this->createSingleReport();
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [
                    'report' => $this->getSingleReport()
                ],
            ];
            return $this->createArrayResponse($response, 'data');
        } catch (\RuntimeException $exception) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $exception->getMessage(),
            ];

            return $this->createArrayResponse($response, 'data');
        }
    }

    /**
     * @return mixed
     */
    public function groupReportCreate()
    {
        try {
            $this->createGroupReport();
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => [
                    'report' => $this->getGroupReport()
                ],
            ];
            return $this->createArrayResponse($response, 'data');
        } catch (\RuntimeException $exception) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $exception->getMessage(),
            ];

            return $this->createArrayResponse($response, 'data');
        }
    }
}
