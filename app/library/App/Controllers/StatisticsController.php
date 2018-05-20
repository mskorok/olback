<?php

namespace App\Controllers;

use App\Constants\Services;
use PhalconRest\Export\Documentation;
use PhalconRest\Export\Postman\ApiCollection;
use PhalconRest\Mvc\Controllers\CollectionController;
use PhalconRest\Transformers\DocumentationTransformer;
use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
use App\Model\UserOrganization;
use App\Model\User;
class StatisticsController extends CollectionController
{

    public static function getUserDetails($userId)
    {
        $user = User::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $userId,
                ],
            ]
        );
        if ($user) {
            $organization = UserOrganization::findFirst(
                [
                    'conditions' => 'user_id = ?1',
                    'bind' => [
                        1 => $userId,
                    ],
                ]
            );

            if ($organization) {
                return array('account' => $user, 'organization' => $organization);
            } else {
                return array('account' => $user, 'organization' => null);
            }
        } else {
            return null;
        }
    }

    public function getDashboardStats()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;
        $connection = $this->db;
        $sql_dist = 'SELECT COUNT(U.id) AS count,role FROM `user` U INNER JOIN user_organization UO ON U.id = UO.user_id WHERE (U.role = \'Manager\' OR U.role = \'User\' ) AND UO.organization_id = '.$organization.' GROUP BY role';
        $data_dist = $connection->query($sql_dist);
        $data_dist->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_USERS = $data_dist->fetchAll();

//SELECT count(id),CASE WHEN status = 0 THEN "stopped" ELSE "running" END FROM `process` WHERE organizationId = 1 GROUP BY status

        $sql_dist_org = 'SELECT count(id) as count,CASE WHEN status = 0 THEN "stopped" ELSE "running" END as status FROM `process` WHERE organizationId = '.$organization.' GROUP BY status';
        $data_dist_org = $connection->query($sql_dist_org);
        $data_dist_org->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_ORGS = $data_dist_org->fetchAll();





        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => array(
                'count_users'=>$COUNT_USERS,
                'count_organizations'=>$COUNT_ORGS
            ),
        ];

        return $this->createArrayResponse($response, 'data');


    }

    public function getReportsByProcess($id){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;
        $connection = $this->db;
        $sql_dist_totals = 'SELECT AVG(answer) as average, COUNT(A.id)as totals FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id WHERE SQ.answered_type = 2 AND S.id IN (SELECT step0 FROM `process` WHERE id = '.$id.' UNION SELECT step3_0 FROM process WHERE id = '.$id.' UNION SELECT step3_1 FROM process WHERE id = '.$id.')';
        $data_dist_totals = $connection->query($sql_dist_totals);
        $data_dist_totals->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_TOTALS = $data_dist_totals->fetchAll();

        $sql_dist_answer = 'SELECT AVG(answer) as average, COUNT(A.id)as totals,SQ.question FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id WHERE SQ.answered_type = 2 AND S.id IN (SELECT step0 FROM `process` WHERE id = '.$id.' UNION SELECT step3_0 FROM process WHERE id = '.$id.' UNION SELECT step3_1 FROM process WHERE id = '.$id.') GROUP BY SQ.id';
        $data_dist_answer = $connection->query($sql_dist_answer);
        $data_dist_answer->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_ANSWERS = $data_dist_answer->fetchAll();

        $sql_dist_GROUP = 'SELECT AVG(answer) as average, COUNT(A.id)as totals,QG.name FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id WHERE SQ.answered_type = 2 AND S.id IN (SELECT step0 FROM `process` WHERE id = '.$id.' UNION SELECT step3_0 FROM process WHERE id = '.$id.' UNION SELECT step3_1 FROM process WHERE id = '.$id.') GROUP BY SQ.question_group_id';
        $data_dist_GROUP = $connection->query($sql_dist_GROUP);
        $data_dist_GROUP->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $COUNT_GROUP = $data_dist_GROUP->fetchAll();



        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => array(
                'totals'=>array(
                    "avg"=>$COUNT_TOTALS[0]['average'],
                    "totals" =>$COUNT_TOTALS[0]['totals'] ),
                'byQuestion'=>$COUNT_ANSWERS,
                'byGroup' => $COUNT_GROUP
            ),
        ];

        return $this->createArrayResponse($response, 'data');


//SELECT AVG(answer), COUNT(A.id)FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id
//SELECT AVG(answer), COUNT(A.id),SQ.question FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id GROUP BY SQ.id
//SELECT AVG(answer), COUNT(A.id),QG.name FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id INNER JOIN survey S ON S.id = SQ.survey_id INNER JOIN question_group QG ON QG.id = SQ.question_group_id GROUP BY SQ.question_group_id
    }


}
