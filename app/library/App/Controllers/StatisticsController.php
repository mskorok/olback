<?php

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Traits\Auth;
use Phalcon\Db;
use PhalconRest\Mvc\Controllers\CollectionController;

class StatisticsController extends CollectionController
{
    use Auth;

    /**
     * @return mixed
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

        $organization = $creator['organization']->organization_id;
        $connection = $this->db;
        $sql_dist = 'SELECT COUNT(U.id) AS count, role FROM `user` U '
            . 'INNER JOIN user_organization UO ON U.id = UO.user_id '
            . 'WHERE (U.role = \'' . AclRoles::MANAGER . '\' OR U.role = \'' . AclRoles::USER
            . '\' ) AND UO.organization_id = '.$organization
            .' GROUP BY role';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(Db::FETCH_ASSOC);
        $COUNT_USERS = $data_dist->fetchAll();

//SELECT count(id),CASE WHEN status = 0 THEN "stopped" ELSE "running" END FROM `process` WHERE organizationId = 1
// GROUP BY status

        $sql_dist_org = 'SELECT COUNT(id) as count, '
            . 'CASE WHEN status = 0 THEN "stopped" ELSE "running" END as status FROM `process` WHERE organizationId = '
            . $organization . ' GROUP BY status';
        $data_dist_org = $connection->query($sql_dist_org);
        $data_dist_org->setFetchMode(Db::FETCH_ASSOC);
        $COUNT_ORGS = $data_dist_org->fetchAll();

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => [
                'count_users'=>$COUNT_USERS,
                'count_organizations'=>$COUNT_ORGS
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
        $COUNT_TOTALS = $data_dist_totals->fetchAll();

        $sql_dist_answer = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals, SQ.question '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' GROUP BY SQ.id';
        $data_dist_answer = $connection->query($sql_dist_answer);
        $data_dist_answer->setFetchMode(Db::FETCH_ASSOC);
        $COUNT_ANSWERS = $data_dist_answer->fetchAll();

        $sql_dist_GROUP = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals,QG.name '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' GROUP BY SQ.question_group_id ';
        $data_dist_GROUP = $connection->query($sql_dist_GROUP);
        $data_dist_GROUP->setFetchMode(Db::FETCH_ASSOC);
        $COUNT_GROUP = $data_dist_GROUP->fetchAll();

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => [
                'totals'=> [
                    'avg'=>$COUNT_TOTALS[0]['average'],
                    'totals' =>$COUNT_TOTALS[0]['totals']],
                'byQuestion'=>$COUNT_ANSWERS,
                'byGroup' => $COUNT_GROUP
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
        $COUNT_TOTALS = $data_dist_totals->fetchAll();

        $sql_dist_answer = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals, SQ.question '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' AND A.userId = '.$userId.' GROUP BY SQ.id';
        $data_dist_answer = $connection->query($sql_dist_answer);
        $data_dist_answer->setFetchMode(Db::FETCH_ASSOC);
        $COUNT_ANSWERS = $data_dist_answer->fetchAll();

        $sql_dist_GROUP = 'SELECT ROUND(AVG(answer)-3,2) as average, COUNT(A.id)as totals,QG.name '
            . 'FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
            . 'INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id '
            . 'WHERE SQ.answered_type = 2 AND S.id = '.$id.' AND A.userId = '.$userId.' GROUP BY SQ.question_group_id ';
        $data_dist_GROUP = $connection->query($sql_dist_GROUP);
        $data_dist_GROUP->setFetchMode(Db::FETCH_ASSOC);
        $COUNT_GROUP = $data_dist_GROUP->fetchAll();

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => [
                'totals'=> [
                    'avg'=>$COUNT_TOTALS[0]['average'],
                    'totals' =>$COUNT_TOTALS[0]['totals']],
                'byQuestion'=>$COUNT_ANSWERS,
                'byGroup' => $COUNT_GROUP
            ],
        ];

        return $this->createArrayResponse($response, 'data');
    }
}
