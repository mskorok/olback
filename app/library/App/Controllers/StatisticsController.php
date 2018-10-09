<?php

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Model\Organization;
use App\Model\Process;
use App\Model\User;
use App\Traits\Auth;
use App\Traits\Processes;
use App\Traits\Stats;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CollectionController;

class StatisticsController extends CollectionController
{
    use Auth, Processes, Stats;

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function getDashboardStats()
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

        $process = $this->getFirstProcessByUser($creator['account']);


        $organizationId = $creator['organization']->organization_id;
        $connection = $this->db;
        $sql_dist = 'SELECT COUNT(U.id) AS count, role FROM `user` U '
            . 'INNER JOIN user_organization UO ON U.id = UO.user_id '
            . 'WHERE (U.role = \'' . AclRoles::MANAGER . '\' OR U.role = \'' . AclRoles::USER
            . '\' ) AND UO.organization_id = '.$organizationId
            .' GROUP BY role';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(Db::FETCH_ASSOC);
        $countUsers = $data_dist->fetchAll();

//SELECT count(id),CASE WHEN status = 0 THEN "stopped" ELSE "running" END FROM `process` WHERE organizationId = 1
// GROUP BY status

        $sql_dist_org = 'SELECT COUNT(id) as count, '
            . '(CASE WHEN status = 0 THEN "stopped" ELSE "running" END) as status '
            . 'FROM `process` WHERE organizationId = ' . $organizationId . ' GROUP BY status';
        $data_dist_org = $connection->query($sql_dist_org);
        $data_dist_org->setFetchMode(Db::FETCH_ASSOC);
        $countOrganizations = $data_dist_org->fetchAll();

        /** @var Organization $organization */
        $organization = Organization::findFirst((int) $organizationId);


        $processes = $organization instanceof Organization ? $organization->getProcess() : [];

        $statusRunningCount = 0;
        $statusStoppedCount = 0;

        /** @var Process $model */
        foreach ($processes as $model) {
            if ((int)$model->status === 1) {
                $statusRunningCount++;
            } else {
                $statusStoppedCount++;
            }
        }

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => [
                'count_users' => $countUsers,
                'count_organizations' => $countOrganizations,
                'process' => $process,
                'running' => $statusRunningCount,
                'stopped' => $statusStoppedCount
            ],
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getReportsBySurvey($id)
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


        $connection = $this->db;
        $sql_dist_totals = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id =  '.$id.' ';
        $data_dist_totals = $connection->query($sql_dist_totals);
        $data_dist_totals->setFetchMode(Db::FETCH_ASSOC);
        $countTotals = $data_dist_totals->fetchAll();

        $sql_dist_answer = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals, SQ.question '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' GROUP BY SQ.id';
        $data_dist_answer = $connection->query($sql_dist_answer);
        $data_dist_answer->setFetchMode(Db::FETCH_ASSOC);
        $countAnswers = $data_dist_answer->fetchAll();

        $sql_dist_GROUP = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals, QG.name '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' GROUP BY SQ.question_group_id ';
        $data_dist_GROUP = $connection->query($sql_dist_GROUP);
        $data_dist_GROUP->setFetchMode(Db::FETCH_ASSOC);
        $countGroup = $data_dist_GROUP->fetchAll();

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => [
                'totals'=> [
                    'avg'=>$countTotals[0]['average'],
                    'totals' =>$countTotals[0]['totals']],
                'byQuestion'=>$countAnswers,
                'byGroup' => $countGroup
            ],
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @param $userId
     * @return mixed
     */
    public function getReportsBySurveyAndUser($id, $userId)
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

        $connection = $this->db;
        $sql_dist_totals = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id =  '.$id.' AND A.userId = '.$userId.' ';
        $data_dist_totals = $connection->query($sql_dist_totals);
        $data_dist_totals->setFetchMode(Db::FETCH_ASSOC);
        $countTotals = $data_dist_totals->fetchAll();

        $sql_dist_answer = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals, SQ.question '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' AND A.userId = '.$userId.' GROUP BY SQ.id';
        $data_dist_answer = $connection->query($sql_dist_answer);
        $data_dist_answer->setFetchMode(Db::FETCH_ASSOC);
        $countAnswers = $data_dist_answer->fetchAll();

        $sql_dist_GROUP = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals,QG.name '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' AND A.userId = '.$userId.' GROUP BY SQ.question_group_id ';
        $data_dist_GROUP = $connection->query($sql_dist_GROUP);
        $data_dist_GROUP->setFetchMode(Db::FETCH_ASSOC);
        $countGroup = $data_dist_GROUP->fetchAll();

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => [
                'totals'=> [
                    'avg'=>$countTotals[0]['average'],
                    'totals' =>$countTotals[0]['totals']],
                'byQuestion'=>$countAnswers,
                'byGroup' => $countGroup
            ],
        ];

        return $this->createArrayResponse($response, 'data');
    }


    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function getDashboardIndices()
    {
        $user = $this->getAuthenticated();
        if ($user instanceof User) {
            $organization = $user->getOrganization();
            if ($organization instanceof Organization) {
                /** @var Simple $processes */
                $processes = $organization->getProcess();
                $results = [];
                /** @var Process $process */
                foreach ($processes as $process) {
                    $results[] = [
                        'process' => $process,
                        'index' => $this->getOlsetIndexesCompare($process),
                        'user' => $this->getParticipatedUsersCompare($process),
                        'absolute' => $this->getAbsoluteOlsetIndexCompare($process)
                    ];
                }
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'data' => $results,
                    'organization' => $organization,
                    'user' => $user
                ];
                return $this->createArrayResponse($response, 'data');
            }
            throw new \RuntimeException('Organization not found');
        }
        throw new  \RuntimeException('User not authorized');
    }
}
