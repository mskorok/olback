<?php

namespace App\Controllers;

use App\Model\Process;
use PhalconRest\Mvc\Controllers\CrudResourceController;
// use PhalconRest\Transformers\Postman\ApiCollectionTransformer;
// use App\Model\Group;
use App\Model\UserOrganization;
use App\Model\User;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use Phalcon\Http\Request;

class SurveyController extends CrudResourceController
{
    public function createSurveyDefinition()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;
        $request = new Request();
        $data = $request->getJsonRawBody();
        $survey = new \App\Model\Survey();
        $survey->title = $data->title;
        $survey->description = $data->description;
        $survey->isEditable = $data->isEditable;
        $survey->isOlset = $data->isOlset;
        $survey->creator = $creator['account']->id;
        $survey->organization_id = $organization;
        if ($survey->save() == false) {
            $messagesErrors = array();
            foreach ($survey->getMessages() as $message) {
                // print_r($message);
                $messagesErrors[] = $message;
            }
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => $messagesErrors,
            ];
        } else {
            $surveyId = $survey->getWriteConnection()->lastInsertId();
            $response = [
              'code' => 1,
              'status' => 'Success',
              'data' => array('surveyId' => $surveyId),
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    public function getSurveyDefinition()
    {
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;
        $surveys = Survey::find(
          [
              'conditions' => '	organization_id = ?1',
              'bind' => [
                  1 => $organization,
              ],
          ]
        );

        $response = [
          'code' => 1,
          'status' => 'Success',
          'data' => $surveys,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function updateSurveyDefinition($id){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }
      $request = new Request();
      $data = $request->getJsonRawBody();
      $creator = $this->getUserDetails($creatorId);
      $organization = $creator['organization']->organization_id;
      if ($creator['organization'] == null) {
          $response = [
        'code' => 0,
        'status' => 'Error',
        'data' => "Manager's organization not found!",
      ];

          return $this->createArrayResponse($response, 'data');
      }


      $survey = Survey::findFirst(
          [
          'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
          'bind' => [
              1 => $id,
              2 => $organization,
              3 => $creator['account']->id
          ],
      ]);

      if ($survey->id) {
      //  echo $department->id;die();
          if (isset($data->title)) {
              $survey->title = $data->title;
          }
          if (isset($data->description)) {
              $survey->description = $data->description;
          }
          if (isset($data->isEditable)) {
              $survey->isEditable = $data->isEditable;
          }
          if (isset($data->isOlset)) {
              $survey->isOlset = $data->isOlset;
          }
          if ($survey->save() == false) {
              $messagesErrors = array();
              foreach ($survey->getMessages() as $message) {
                  // print_r($message);
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
            'status' => 'You cannot edit this survey!',
          ];
      }

        return $this->createArrayResponse($response, 'data');
    }

    public function createQuestion($id){

          if ($this->authManager->loggedIn()) {
              $session = $this->authManager->getSession();
              $creatorId = $session->getIdentity();
          }
          $creator = $this->getUserDetails($creatorId);

          $organization = $creator['organization']->organization_id;
          $request = new Request();
          $data = $request->getJsonRawBody();

          $survey = Survey::findFirst(
              [
              'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
              'bind' => [
                  1 => $id,
                  2 => $organization,
                  3 => $creator['account']->id
              ],
          ]);

          if ($survey->id) {
            $surveyQuestion = new \App\Model\SurveyQuestion();
            $surveyQuestion->question = $data->question;
            $surveyQuestion->description = $data->description;
            $surveyQuestion->answered_type = $data->answered_type;
            $surveyQuestion->question_order = $data->question_order;
            $surveyQuestion->survey_id = $id;
            if ($surveyQuestion->save() == false) {
                $messagesErrors = array();
                foreach ($surveyQuestion->getMessages() as $message) {
                    // print_r($message);
                    $messagesErrors[] = $message;
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors,
                ];
            } else {
                $surveyId = $surveyQuestion->getWriteConnection()->lastInsertId();
                $response = [
                  'code' => 1,
                  'status' => 'Success',
                  'data' => array('surveyQuestion' => $surveyId),
                ];
            }
          }else{
            $response = [
                'code' => 0,
                'status' => 'Unauthorized user!',
            ];
          }





          return $this->createArrayResponse($response, 'data');
    }

    public function getQuestion($id){
      if ($this->authManager->loggedIn()) {
          $session = $this->authManager->getSession();
          $creatorId = $session->getIdentity();
      }

      $creator = $this->getUserDetails($creatorId);
      $organization = $creator['organization']->organization_id;
      $survey = Survey::findFirst(
          [
          'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
          'bind' => [
              1 => $id,
              2 => $organization,
              3 => $creator['account']->id
          ],
      ]);

      if ($survey->id) {
        
      $surveyQuestion = SurveyQuestion::find(
        [
          'conditions' => 'survey_id = ?1',
          'bind' => [
              1 => $id
       ]]
      );

      $response = [
        'code' => 1,
        'status' => 'Success',
        'data' => $surveyQuestion,
      ];
    }else{
      $response = [
          'code' => 0,
          'status' => 'Unauthorized user!',
      ];
    }
      return $this->createArrayResponse($response, 'data');
    }

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

    public function createAnswer(){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }

        $creator = $this->getUserDetails($creatorId);

        $request = new Request();
        $data = $request->getJsonRawBody();

        foreach ($data as $answer) {
            $answerModel = new \App\Model\Answer();
            $answerModel->answer = $answer->answer;
            $answerModel->userId = $creator['account']->id;
            $answerModel->questionId = $answer->questionId;
            $answerModel->save() ;
            }


        $response = [
        'code' => 1,
        'status' => 'Success'];

        return $this->createArrayResponse($response, 'data');
    }

    public function initProcess($id){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;

        $proc = Process::findFirst(
            [
                'conditions' => 'id = ?1 AND step0 IS NULL',
                'bind' => [
                    1 => $id
                ],
            ]);
        if($proc->id) {
            //create step0
            $step0 = new \App\Model\Survey();
            $step0->title = "The self-evaluation questionnaire";
            $step0->description = "";
            $step0->isEditable = 0;
            $step0->isOlset = 1;
            $step0->creator = $creator['account']->id;
            $step0->organization_id = $organization;

            $step0->save();

            $step0_ID = $step0->getWriteConnection()->lastInsertId();

            //create step3_0
            $step3_0 = new \App\Model\Survey();
            $step3_0->title = "The self-evaluation questionnaire";
            $step3_0->description = "";
            $step3_0->isEditable = 0;
            $step3_0->isOlset = 1;
            $step3_0->creator = $creator['account']->id;
            $step3_0->organization_id = $organization;
            $step3_0->save();
            $step3_0_ID = $step3_0->getWriteConnection()->lastInsertId();

            //create step3_1
            $step3_1 = new \App\Model\Survey();
            $step3_1->title = "Micro tool/guidelines";
            $step3_1->description = "On a daily basis Step 3 is performed through the tool named After Action Review (AAR)15. AAR is a
sequence of four questions intended for use right after the conclusion of each individual action/project,
practically enacting and forcing double-loop learning. The four questions are:
";
            $step3_1->isEditable = 0;
            $step3_1->isOlset = 1;
            $step3_1->creator = $creator['account']->id;
            $step3_1->organization_id = $organization;
            $step3_1->save();
            $step3_1_ID = $step3_1->getWriteConnection()->lastInsertId();

            $connection = $this->db;
            //create questions
            $sqlStep0 = "INSERT INTO `survey_questions` (`id`, `question`, `description`, `answered_type`, `question_order`, `survey_id`) VALUES
(NULL, '1. Co-operation agreements with other companies, universities, technical colleges, experts, etc. are promoted.', NULL, 2, 1, $step0_ID),
(NULL, '2. The organization encourages its employees in various practical ways to join formal or informal networks.', NULL, 2, 2, $step0_ID),
(NULL, '3. New ideas and approaches on work performance are experimented with continually.', NULL, 2, 3, $step0_ID),
(NULL, '4. Organizational systems and procedures support innovation.', NULL, 2, 4, $step0_ID),
(NULL, '5. The company has formal mechanisms to guarantee the sharing of best practices among the different fields of activity.', NULL, 2, 5, $step0_ID),
(NULL, '6. There are individuals within the organization who take part in several teams or divisions and who also act as links between them.', NULL, 2, 6, $step0_ID),
(NULL, '7. There are individuals responsible for collecting, assembling and distributing employees’ suggestions internally.', NULL, 2, 7, $step0_ID),
(NULL, '8. The company offers internal opportunities to learn (visits to other parts of the organization, in- ternal training programs, etc.) so as to make individuals aware of other people’s or departments’ duties and share employees knowledge and experience.', NULL, 2, 8, $step3_0_ID),
(NULL, '9. Teamwork is a very common practice in the company.', NULL, 2, 9, $step0_ID),
(NULL, '10. All the members of the organization share the same aim, to which they feel committed.', NULL, 2, 10, $step0_ID),
(NULL, '11. The company has databases or other means to store its experiences and knowledge so as to be able to use them later on.', NULL, 2, 11, $step0_ID),
(NULL, '12. Databases are always kept up to date.', NULL, 2, 12, $step0_ID),
(NULL, '13. All the employees in the organization have access to the organization’s databases.', NULL, 2, 13, $step0_ID),
(NULL, '14. The codification and knowledge administration system makes work easier for employees.', NULL, 2, 14, $step0_ID),
(NULL, '15. My organization encourages people to think from a community perspective.', NULL, 2, 15, $step0_ID),
(NULL, '16. My organization works together with the outside community to meet mutual needs.', NULL, 2, 16, $step0_ID),
(NULL, '17. In my organization, leaders ensure that the organization’s actions are consistent with its values.', NULL, 2, 17, $step0_ID),
(NULL, '18. My organization builds alignment of visions across different levels and work groups.', NULL, 2, 18, $step0_ID),
(NULL, '19. My organization considers the impact of decisions on employee morale.', NULL, 2, 19, $step0_ID),
(NULL, '20. My organization encourages people to get answers from across the organization when solving problems.', NULL, 2, 20, $step0_ID),
(NULL, '21. In my organization, people openly discuss mistakes in order to learn from them.', NULL, 2, 21, $step0_ID),
(NULL, '22. In my organization, people give open and honest feedback to each other.', NULL, 2, 22, $step0_ID),
(NULL, '23. In my organization, people view problems in their work as an opportunity to learn.', NULL, 2, 23, $step0_ID),
(NULL, '24. In my organization, people are rewarded for exploring new ways of working.', NULL, 2, 24, $step0_ID),
(NULL, '25. My organization recognizes people for taking the initiative.', NULL, 2, 25, $step0_ID),
(NULL, '26. My organization gives people control over the resources they need to accomplish their work.', NULL, 2, 26, $step0_ID),
(NULL, '27. In my organization, leaders generally support requests for learning opportunities.', NULL, 2, 27, $step0_ID),
(NULL, '28. In my organization, investment in workers’ skills and professional development is greater than last year.', NULL, 2, 28, $step0_ID)";
            $connection->query($sqlStep0);


            //create questions
            $sqlStep3_0 = "INSERT INTO `survey_questions` (`id`, `question`, `description`, `answered_type`, `question_order`, `survey_id`) VALUES
(NULL, '1. Co-operation agreements with other companies, universities, technical colleges, experts, etc. are promoted.', NULL, 2, 1, $step3_0_ID),
(NULL, '2. The organization encourages its employees in various practical ways to join formal or informal networks.', NULL, 2, 2, $step3_0_ID),
(NULL, '3. New ideas and approaches on work performance are experimented with continually.', NULL, 2, 3, $step3_0_ID),
(NULL, '4. Organizational systems and procedures support innovation.', NULL, 2, 4, $step3_0_ID),
(NULL, '5. The company has formal mechanisms to guarantee the sharing of best practices among the different fields of activity.', NULL, 2, 5, $step3_0_ID),
(NULL, '6. There are individuals within the organization who take part in several teams or divisions and who also act as links between them.', NULL, 2, 6, $step3_0_ID),
(NULL, '7. There are individuals responsible for collecting, assembling and distributing employees’ suggestions internally.', NULL, 2, 7, $step3_0_ID),
(NULL, '8. The company offers internal opportunities to learn (visits to other parts of the organization, in- ternal training programs, etc.) so as to make individuals aware of other people’s or departments’ duties and share employees knowledge and experience.', NULL, 2, 8, $step3_0_ID),
(NULL, '9. Teamwork is a very common practice in the company.', NULL, 2, 9, $step3_0_ID),
(NULL, '10. All the members of the organization share the same aim, to which they feel committed.', NULL, 2, 10, $step3_0_ID),
(NULL, '11. The company has databases or other means to store its experiences and knowledge so as to be able to use them later on.', NULL, 2, 11, $step3_0_ID),
(NULL, '12. Databases are always kept up to date.', NULL, 2, 12, $step3_0_ID),
(NULL, '13. All the employees in the organization have access to the organization’s databases.', NULL, 2, 13, $step3_0_ID),
(NULL, '14. The codification and knowledge administration system makes work easier for employees.', NULL, 2, 14, $step3_0_ID),
(NULL, '15. My organization encourages people to think from a community perspective.', NULL, 2, 15, $step3_0_ID),
(NULL, '16. My organization works together with the outside community to meet mutual needs.', NULL, 2, 16, $step3_0_ID),
(NULL, '17. In my organization, leaders ensure that the organization’s actions are consistent with its values.', NULL, 2, 17, $step3_0_ID),
(NULL, '18. My organization builds alignment of visions across different levels and work groups.', NULL, 2, 18, $step3_0_ID),
(NULL, '19. My organization considers the impact of decisions on employee morale.', NULL, 2, 19, $step3_0_ID),
(NULL, '20. My organization encourages people to get answers from across the organization when solving problems.', NULL, 2, 20, $step3_0_ID),
(NULL, '21. In my organization, people openly discuss mistakes in order to learn from them.', NULL, 2, 21, $step3_0_ID),
(NULL, '22. In my organization, people give open and honest feedback to each other.', NULL, 2, 22, $step3_0_ID),
(NULL, '23. In my organization, people view problems in their work as an opportunity to learn.', NULL, 2, 23, $step3_0_ID),
(NULL, '24. In my organization, people are rewarded for exploring new ways of working.', NULL, 2, 24, $step3_0_ID),
(NULL, '25. My organization recognizes people for taking the initiative.', NULL, 2, 25, $step3_0_ID),
(NULL, '26. My organization gives people control over the resources they need to accomplish their work.', NULL, 2, 26, $step3_0_ID),
(NULL, '27. In my organization, leaders generally support requests for learning opportunities.', NULL, 2, 27, $step3_0_ID),
(NULL, '28. In my organization, investment in workers’ skills and professional development is greater than last year.', NULL, 2, 28, $step3_0_ID)";
            $connection->query($sqlStep3_0);


            $sqlStep3_1 = "INSERT INTO `survey_questions` ( `question`, `description`, `answered_type`, `question_order`, `survey_id`) VALUES
( 'What was the purpose(s) of doing this action/project?', NULL, 1, 1, $step3_1_ID),
( 'Did we achieve it and how can we tell/know? (what are the observable facts indicating clearly\r\nthat we achieved or not the purpose(s) identified through the previous question?)', NULL, 1, 1, $step3_1_ID),
( 'If we were to repeat the same action/project now, what would we do the same? Why?', NULL, 1, 1, $step3_1_ID)";
            $connection->query($sqlStep3_1);


            $process = Process::findFirst(
                [
                    'conditions' => 'id = ?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]);

            $process->step0 = (int)$step0_ID;
            $process->step3_0 = (int)$step3_0_ID;
            $process->step3_1 = (int)$step3_1_ID;
            $process->organizationId  = $organization;
            $process->save();
        }
        $response = [
            'code' => 1,
            'status' => 'Success'
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function changeProcessStatus($id){
        $proc = Process::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $id
                ],
            ]);
        if($proc->status == 1){
            $proc->status = 0; //set stop
            $statusDesc = "stopped";
        }else{
            $proc->status = 1; //set running
            $statusDesc = "running";
        }


        $proc->save();

        $response = [
            'current_status' => $statusDesc,
            'status' => 'Success'
        ];

        return $this->createArrayResponse($response, 'data');

    }

    public function getUserSurveyAnswers(){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);
        $sql = 'SELECT questionId,question,answer,question_order,survey_id FROM `answers` A INNER JOIN survey_questions SQ ON A.questionId = SQ.id WHERE A.userId = '.$creatorId.' ';
        $connection = $this->db;
        $data = $connection->query($sql);
        $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $iresults = $data->fetchAll();
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $iresults,
        ];

        return $this->createArrayResponse($response, 'data');
    }


    public function getSurveyAnswers($id){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $creator = $this->getUserDetails($creatorId);

        $organization = $creator['organization']->organization_id;
        $sql = 'SELECT questionId,question,answer,question_order,survey_id,userId FROM survey S INNER JOIN survey_questions SQ ON S.id = SQ.survey_id  LEFT JOIN answers A ON SQ.id = A.questionId WHERE S.organization_id = '.$organization.' AND S.id = '.$id.'  ';
        $connection = $this->db;
        $data = $connection->query($sql);
        $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $iresults = $data->fetchAll();
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $iresults,
        ];

        return $this->createArrayResponse($response, 'data');
    }


    public function helpPage(){

        $request = new Request();
        $data = $request->getJsonRawBody();

        $to_slug = $data->slug;
        $find_help_post = array(
            'name'        => $to_slug,
            'post_type'   => 'help',
            'post_status' => 'publish'
        );

        $help_post_result = get_posts($find_help_post);

        if (empty($help_post_result)) {

            $help_post = array(
                'post_title'    => $to_slug,
                'post_name'		=> $to_slug,
                'post_type'		=> 'help',
                'post_status'   => 'publish'
            );

            $help_post_id = wp_insert_post( $help_post );

            $response = [
                'code' => 0,
                'status' => 'Success',
                'msg' => 'page not exists created just now with id: ' . $help_post_id,
            ];

            return $this->createArrayResponse($response, 'data');
        } else {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => $help_post_result[0]->post_content,
            ];
            return $this->createArrayResponse($response, 'data');

        }
    }


    public function availableUserSurveys(){
        if ($this->authManager->loggedIn()) {
            $session = $this->authManager->getSession();
            $creatorId = $session->getIdentity();
        }
        $user = $this->getUserDetails($creatorId);
        $sql_getProcesses = 'SELECT PR.id,PR.`step0`, PR.`step3_0`, PR.`step3_1`
                FROM `process` PR
                INNER JOIN survey S ON PR.`step0`= S.id OR PR.`step3_0`= S.id OR PR.`step3_1`= S.id
                WHERE PR.id IN (SELECT  `processId` FROM `process_departments` WHERE `departmentId` IN (SELECT department_id FROM user_department WHERE user_id =  '.$user.' )) OR
                PR.id IN (SELECT  `processId` FROM `process_organizations` WHERE `organizationId` IN (SELECT organization_id FROM user_organization WHERE user_id =  '.$user.' )) OR
                PR.id IN (SELECT `processId` FROM `process_users` WHERE userId = '.$user.' ) GROUP BY PR.`step0`, PR.`step3_0`, PR.`step3_1`';
        $connection = $this->db;
        $data = $connection->query($sql_getProcesses);
        $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        foreach ($data as $val) {
            echo $val['id'];
        }
die();

    }
}
