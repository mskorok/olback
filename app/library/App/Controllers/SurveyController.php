<?php

namespace App\Controllers;

use App\Constants\Services;
use App\Model\Answer;
use App\Model\Process;
use App\Model\QuestionGroups;
use App\Model\SurveyTemplate;
use App\Model\SystemicMapItems;
use App\Traits\Auth;
use App\Traits\Surveys;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\Survey;
use App\Model\SurveyQuestion;

class SurveyController extends CrudResourceController
{
    use Auth, Surveys;

    /**
     * @return mixed
     */
    public function createSurveyDefinition()
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

        $data = $this->request->getJsonRawBody();
        $survey = new SurveyTemplate();
        $survey->title = $data->title;
        $survey->description = $data->description;
        $survey->isEditable = $data->isEditable;
        $survey->isOlset = $data->isOlset;
        $survey->creator = $creator['account']->id;
        $survey->organization_id = $organization;
        if (property_exists($data, 'show_extra_info_and_tags')) {
            $survey->show_extra_info_and_tags = $data->show_extra_info_and_tags;
        } else {
            $survey->show_extra_info_and_tags = false;
        }
        if (property_exists($data, 'tag')) {
            $survey->tag = $data->tag;
        }
        if (property_exists($data, 'extra_info')) {
            $survey->extra_info = $data->extra_info;
        } else {
            $survey->extra_info = '';
        }

        if ($survey->save() === false) {
            $messagesErrors = [];
            foreach ($survey->getMessages() as $message) {
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
                'data' => ['surveyId' => $surveyId],
            ];
        }

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function getSurveyDefinition()
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
        $organization = $creator ? $creator['organization']->organization_id : null;
        $surveys = SurveyTemplate::find(
            [
                'conditions' => '	organization_id = ?1 AND isOlset = 0 ',
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

    /**
     * @param $id
     * @return mixed
     */
    public function updateSurveyDefinition($id)
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
        $data = $this->request->getJsonRawBody();
        $creator = static::getUserDetails($creatorId);
        $organization = $creator['organization']->organization_id;
        if ($creator && $creator['organization'] === null) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => "Manager's organization not found!",
            ];

            return $this->createArrayResponse($response, 'data');
        }


        $survey = SurveyTemplate::findFirst(
            [
                'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
                'bind' => [
                    1 => $id,
                    2 => $organization,
                    3 => $creator['account']->id
                ],
            ]
        );

        if ($survey instanceof SurveyTemplate) {
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
            if ($survey->save() === false) {
                $messagesErrors = [];
                foreach ($survey->getMessages() as $message) {
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

    /**
     * @param $id
     * @return mixed
     */
    public function createQuestion($id)
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

        $data = $this->request->getJsonRawBody();

        $survey = SurveyTemplate::findFirst(
            [
                'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
                'bind' => [
                    1 => $id,
                    2 => $organization,
                    3 => $creator['account']->id
                ],
            ]
        );

        if ($survey instanceof SurveyTemplate) {
            $surveyQuestion = $this->createSurveyQuestion($data, $id, true);

            if ($surveyQuestion->save() === false) {
                $messagesErrors = [];
                foreach ($surveyQuestion->getMessages() as $message) {
                    $messagesErrors[] = $message;
                }
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $messagesErrors,
                ];
            } else {
                $surveyQuestion->refresh();
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'data' => ['surveyQuestion' => $surveyQuestion->id],
                ];
            }
        } else {
            $response = [
                'code' => 0,
                'status' => 'Unauthorized user!',
            ];
        }


        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function getQuestionGroups()
    {
        $questionGroups = QuestionGroups::find();
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $questionGroups,
        ];
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getQuestion($id)
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

        $survey = Survey::findFirst((int) $id);

        if ($survey instanceof Survey) {
            $process = $survey->getProcess0();
            if (!($process instanceof Process)) {
                $process = $survey->getProcess30();
                if (!($process instanceof Process)) {
                    $process = $survey->getProcess31();
                    if (!($process instanceof Process)) {
                        $process = $survey->getProcessReality();
                        if (!($process instanceof Process)) {
                            $process = $survey->getProcessVision();
                        }
                    }
                }
            }
            /** @var Simple $surveyQuestion */
            $surveyQuestion = SurveyQuestion::find(
                [
                    'conditions' => 'survey_id = ?1',
                    'bind' => [
                        1 => $id
                    ],
                    'order' => 'question_group_id'
                ]
            );


            $groups = [];
            $flag = 0;
            /** @var SurveyQuestion $item */
            foreach ($surveyQuestion as $item) {
                if ($item->question_group_id !== null && $flag < $item->question_group_id) {
                    $flag = $item->question_group_id;
                    $groups[$item->id] = [$item->id, 'name' => $item->getQuestionGroup()->name];
                } else {
                    $groups[$item->id] = ['id' => $item->id, 'name' => ''];
                }
            }

            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => $surveyQuestion,
                'groups' => $groups,
                'process' => $process,
                'isActionAAR' => !($process instanceof Process)
            ];
        } else {
            $response = [
                'code' => 0,
                'status' => 'Unauthorized user!',
            ];
        }
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function createAnswer()
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

        $data = $this->request->getJsonRawBody();


        foreach ($data as $answer) {
            $oldAnswer = Answer::findFirst([
                'conditions' => 'questionId = ?1',
                'bind' => [
                    1 => $answer->questionId
                ],
            ]);
            $answerModel = $oldAnswer instanceof Answer ? $oldAnswer : new Answer();
            $answerModel->answer = $answer->answer;
            $answerModel->userId = $creator['account']->id;
            $answerModel->questionId = $answer->questionId;
            $answerModel->save();
        }


        $response = [
            'code' => 1,
            'status' => 'Success'];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function initProcess($id)
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

        $process = Process::findFirst(
            [
                'conditions' => 'id = ?1 AND step0 IS NULL',
                'bind' => [
                    1 => $id
                ],
            ]
        );
        if ($process instanceof Process) {
            $this->processId = $process->id;
            //create step0 (initial survey)
            try {
                $step0_ID = $this->createEvaluationSurvey();
            } catch (\RuntimeException $exception) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => [$exception->getMessage()]
                ];
                return $this->createArrayResponse($response, 'data');
            }

            //create step3_0 (evaluation survey)
            try {
                $step3_0_ID = $this->createEvaluationSurvey();
            } catch (\RuntimeException $exception) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => [$exception->getMessage()]
                ];
                return $this->createArrayResponse($response, 'data');
            }

            //create step3_1 (after action review survey)
            try {
                $step3_1_ID = $this->createAfterActionReviewSurvey();
            } catch (\RuntimeException $exception) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => [$exception->getMessage()]
                ];
                return $this->createArrayResponse($response, 'data');
            }

            //create current situation survey
            try {
                $reality = $this->createCurrentSituationSurvey();
            } catch (\RuntimeException $exception) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => [$exception->getMessage()]
                ];
                return $this->createArrayResponse($response, 'data');
            }

            //create vision survey
            try {
                $vision = $this->createVisionSurvey();
            } catch (\RuntimeException $exception) {
                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => [$exception->getMessage()]
                ];
                return $this->createArrayResponse($response, 'data');
            }



            //update process
            $process->step0 = $step0_ID;
            $process->step3_0 = $step3_0_ID;
            $process->step3_1 = $step3_1_ID;
            $process->reality = $reality;
            $process->vision = $vision;
            $process->organizationId = $organization;
            if ($process->save()) {
                $process->refresh();
//                try {
//                    $this->initYearProcesses((int) $id);
//                } catch (\RuntimeException $exception) {
//                    $response = [
//                        'code' => 0,
//                        'status' => 'Error',
//                        'data' => [$exception->getMessage()]
//                    ];
//                    return $this->createArrayResponse($response, 'data');
//                }
            } else {
                foreach ([$step0_ID, $step3_0_ID, $step3_1_ID, $reality, $vision] as $sid) {
                    $survey = Survey::findFirst($sid);
                    if ($survey instanceof Survey) {
                        $survey->delete();
                    }
                }

                $response = [
                    'code' => 0,
                    'status' => 'Error',
                    'data' => $process->getMessages(),
                ];
                return $this->createArrayResponse($response, 'data');
            }
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $process
        ];

        return $this->createArrayResponse($response, 'data');
    }

    public function createActionAAR($id)
    {
        $action = SystemicMapItems::findFirst((int) $id);

        if (!($action instanceof SystemicMapItems)) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'Action not found'
            ];
            return $this->createArrayResponse($response, 'data');
        }

        try {
            $this->extra_info = 'Action id = '.$action->id.' After Action Review for actions';
            $surveyId = $this->createAfterActionReviewSurvey();
        } catch (\RuntimeException $exception) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => [$exception->getMessage()]
            ];
            return $this->createArrayResponse($response, 'data');
        }

        $action->survey = $surveyId;

        if ($action->save()) {
            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => $surveyId
            ];
            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 0,
            'status' => 'Error',
            'data' => 'Action not saved'
        ];
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function changeProcessStatus($id)
    {
        $proc = Process::findFirst((int) $id);

        $statusDesc = 'stopped';
        if ($proc instanceof Process) {
            if ($proc->status === 1) {
                $proc->status = 0; //set stop
                $statusDesc = 'stopped';
            } else {
                $proc->status = 1; //set running
                $statusDesc = 'running';
            }


            $proc->save();
        }

        $response = [
            'current_status' => $statusDesc,
            'status' => 'Success'
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function getUserSurveyAnswers()
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

        $sql = 'SELECT questionId,question,answer,question_order,survey_id FROM `answers` A '
            . 'INNER JOIN survey_questions SQ ON A.questionId = SQ.id WHERE A.userId = ' . $creatorId . ' ';
        $connection = $this->db;
        $data = $connection->query($sql);
        $data->setFetchMode(Db::FETCH_ASSOC);
        $iresults = $data->fetchAll();
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $iresults,
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSurveyAnswers($id)
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
        $sql = 'SELECT questionId,question,answer,question_order,survey_id,userId FROM survey S '
            . 'INNER JOIN survey_questions SQ ON S.id = SQ.survey_id  LEFT JOIN answers A ON SQ.id = A.questionId '
            . 'WHERE S.organization_id = ' . $organization . ' AND S.id = ' . $id . '  ';
        $connection = $this->db;
        $data = $connection->query($sql);
        $data->setFetchMode(Db::FETCH_ASSOC);
        $iresults = $data->fetchAll();
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $iresults,
        ];

        return $this->createArrayResponse($response, 'data');
    }


    public function helpPage()//todo
    {
//        $data = $this->request->getJsonRawBody();
//
//        $to_slug = $data->slug;
//        $find_help_post = [
//            'name' => $to_slug,
//            'post_type' => 'help',
//            'post_status' => 'publish'
//        ];
//
//        $help_post_result = $find_help_post;//get_posts($find_help_post);
//
//        if (empty($help_post_result)) {
//            $help_post = [
//                'post_title' => $to_slug,
//                'post_name' => $to_slug,
//                'post_type' => 'help',
//                'post_status' => 'publish'
//            ];
//
//            $help_post_id = $help_post;//wp_insert_post($help_post);
//
//            $response = [
//                'code' => 0,
//                'status' => 'Success',
//                'msg' => 'page not exists created just now with id: ' . $help_post_id,
//            ];
//
//            return $this->createArrayResponse($response, 'data');
//        }
//        $response = [
//            'code' => 1,
//            'status' => 'Success',
//            'data' => $help_post_result[0]->post_content,
//        ];
//
//        $response = [
//            'code' => 0,
//            'status' => 'No data',
//        ];
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => '<div>There will be<br> help data</div>',
        ];
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function availableUserSurveys()
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

        $config = $this->getDI()->get(Services::CONFIG);

        $sql_getProcesses = 'SELECT PR.id,PR.title, PR.`step0`, PR.`step3_0`, PR.`step3_1` FROM `process` PR '
            . 'INNER JOIN survey S ON PR.`step0`= S.id OR PR.`step3_0`= S.id OR PR.`step3_1`= S.id '
            . 'WHERE PR.id IN (SELECT  `processId` FROM `process_departments` WHERE `departmentId` '
            . 'IN (SELECT department_id FROM user_department WHERE user_id =  ' . $creatorId . ' )) OR '
            . 'PR.id IN (SELECT  `processId` FROM `process_organizations` WHERE `organizationId` '
            . 'IN (SELECT organization_id FROM user_organization WHERE user_id =  ' . $creatorId . ' )) OR '
            . 'PR.id IN (SELECT `processId` FROM `process_users` WHERE userId = ' . $creatorId . ' ) '
            . 'GROUP BY PR.`step0`, PR.`step3_0`, PR.`step3_1`,PR.`id`';
        $connection = $this->db;
        $data = $connection->query($sql_getProcesses);
        $data->setFetchMode(Db::FETCH_ASSOC);
        $iresults = $data->fetchAll();
        $processes = [];
        foreach ($iresults as $val) {
            $sql_isCompleted_step0 = 'SELECT count(A.id) as countAnswers,S.title FROM `answers` A '
                . 'INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
                . 'INNER JOIN survey S ON SQ.survey_id = S.id  WHERE A.userId = '
                . $config->application->admin . ' AND SQ.survey_id = '
                . $val['step0'] . ';';
            $data_isCompleted_step0 = $connection->query($sql_isCompleted_step0);
            $data_isCompleted_step0->setFetchMode(Db::FETCH_ASSOC);
            $iresults_isCompleted_step0 = $data_isCompleted_step0->fetchAll();

            $sql_isCompleted_step3_0 = 'SELECT count(A.id) as countAnswers,S.title FROM `answers` A '
                . 'INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
                . 'INNER JOIN survey S ON SQ.survey_id = S.id  WHERE A.userId ='
                . $config->application->admin . ' AND SQ.survey_id = '
                . $val['step3_0'] . ';';
            $data_isCompleted_step3_0 = $connection->query($sql_isCompleted_step3_0);
            $data_isCompleted_step3_0->setFetchMode(Db::FETCH_ASSOC);
            $iresults_isCompleted_step3_0 = $data_isCompleted_step3_0->fetchAll();

            $sql_isCompleted_step3_1 = 'SELECT count(A.id) as countAnswers,S.title FROM `answers` A '
                . 'INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
                . 'INNER JOIN survey S ON SQ.survey_id = S.id  WHERE A.userId ='
                . $config->application->admin . ' AND SQ.survey_id = '
                . $val['step3_1'] . ';';
            $data_isCompleted_step3_1 = $connection->query($sql_isCompleted_step3_1);
            $data_isCompleted_step3_1->setFetchMode(Db::FETCH_ASSOC);
            $iresults_isCompleted_step3_1 = $data_isCompleted_step3_1->fetchAll();


            $processes[] = [
                'processId' => $val['id'],
                'process_title' => $val['title'],
                'surveys' => [
                    'step0' => [
                        'id' => $val['step0'],
                        'title' => $iresults_isCompleted_step0[0]['title'],
                        'isCompleted' => $iresults_isCompleted_step0[0]['countAnswers'] > 0 ? 1 : 0
                    ],
                    'step3_0' => [
                        'id' => $val['step3_0'],
                        'title' => $iresults_isCompleted_step3_0[0]['title'],
                        'isCompleted' => $iresults_isCompleted_step3_0[0]['countAnswers'] > 0 ? 1 : 0
                    ],
                    'step3_1' => [
                        'id' => $val['step3_1'],
                        'title' => $iresults_isCompleted_step3_1[0]['title'],
                        'isCompleted' => $iresults_isCompleted_step3_1[0]['countAnswers'] > 0 ? 1 : 0
                    ]
                ]
            ];
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $processes
        ];
        return $this->createArrayResponse($response, 'data');
    }
}
