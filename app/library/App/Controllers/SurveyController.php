<?php

namespace App\Controllers;

use App\Constants\Services;
use App\Model\Answer;
use App\Model\Process;
use App\Model\QuestionGroups;
use App\Model\SurveyTemplate;
use App\Traits\Auth;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\SurveyTemplateQuestion;

class SurveyController extends CrudResourceController
{
    use Auth;

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
            $surveyQuestion = new SurveyTemplateQuestion();
            $surveyQuestion->question = $data->question;
            $surveyQuestion->description = $data->description;
            $surveyQuestion->answered_type = $data->answered_type;
            $surveyQuestion->question_order = $data->question_order;
            $surveyQuestion->survey_id = $id;
            if (property_exists($data, 'question_group_id')) {
                $surveyQuestion->question_group_id = $data->question_group_id;
            }
            if (property_exists($data, 'showExtraInfoAndTags')) {
                $surveyQuestion->showExtraInfoAndTags = $data->showExtraInfoAndTags;
            } else {
                $surveyQuestion->showExtraInfoAndTags = false;
            }
            if (property_exists($data, 'extraInfo')) {
                $surveyQuestion->extraInfo = $data->extraInfo;
            } else {
                $surveyQuestion->extraInfo = '';
            }
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
                $surveyId = $surveyQuestion->getWriteConnection()->lastInsertId();
                $response = [
                    'code' => 1,
                    'status' => 'Success',
                    'data' => ['surveyQuestion' => $surveyId],
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

        $survey = Survey::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $id
                ],
            ]
        );

        if ($survey instanceof Survey) {
            /** @var Simple $surveyQuestion */
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
            $answerModel = new Answer();
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

        $proc = Process::findFirst(
            [
                'conditions' => 'id = ?1 AND step0 IS NULL',
                'bind' => [
                    1 => $id
                ],
            ]
        );
        if ($proc instanceof Process) {
            $surveyTemplate = SurveyTemplate::findFirst(
                [
                    'conditions' => 'tag LIKE "%0#3_0%"',
                    'bind' => [
                    ],
                ]
            );

            $step3_0_ID = 0;
            $step0_ID = 0;
            if ($surveyTemplate instanceof SurveyTemplate) {
                //create step0
                $step0 = new Survey();
                $step0->title = $surveyTemplate->title;
                $step0->description = $surveyTemplate->description;
                $step0->isEditable = $surveyTemplate->isEditable;
                $step0->isOlset = $surveyTemplate->isOlset;
                $step0->creator = $surveyTemplate->creator;
                $step0->organization_id = $surveyTemplate->organization_id;

                $step0->save();

                $step0_ID = $step0->getWriteConnection()->lastInsertId();

                //create step3_0
                $step3_0 = new Survey();
                $step3_0->title = $surveyTemplate->title;
                $step3_0->description = $surveyTemplate->description;
                $step3_0->isEditable = $surveyTemplate->isEditable;
                $step3_0->isOlset = $surveyTemplate->isOlset;
                $step3_0->creator = $surveyTemplate->creator;
                $step3_0->organization_id = $surveyTemplate->organization_id;
                $step3_0->save();
                $step3_0_ID = $step3_0->getWriteConnection()->lastInsertId();
            }


            $surveyTemplate2 = SurveyTemplate::findFirst(
                [
                    'conditions' => 'tag LIKE "%3_1%"',
                    'bind' => [
                    ],
                ]
            );

            $step3_1_ID = 0;
            if ($surveyTemplate2 instanceof SurveyTemplate) {
                //create step3_1
                $step3_1 = new Survey();
                $step3_1->title = $surveyTemplate2->title;
                $step3_1->description = $surveyTemplate2->description;
                $step3_1->isEditable = $surveyTemplate2->isEditable;
                $step3_1->isOlset = $surveyTemplate2->isOlset;
                $step3_1->creator = $surveyTemplate2->creator;
                $step3_1->organization_id = $surveyTemplate2->organization_id;
                $step3_1->save();
                $step3_1_ID = $step3_1->getWriteConnection()->lastInsertId();
            }

            //create questions

            /** @var Simple $surveyTemplateQuestions */
            $surveyTemplateQuestions = SurveyTemplateQuestion::find(
                [
                    'conditions' => 'survey_id = ?1 ',
                    'bind' => [
                        1 => $surveyTemplate->id
                    ]
                ]
            );

            /** @var SurveyTemplateQuestion $temp_question */
            foreach ($surveyTemplateQuestions as $temp_question) {
                $question = new SurveyQuestion();
                $question->question = $temp_question->question;
                $question->description = $temp_question->description;
                $question->answered_type = $temp_question->answered_type;
                $question->question_order = $temp_question->question_order;
                $question->survey_id = $step0_ID;

                if (property_exists($temp_question, 'question_group_id')) {
                    $question->question_group_id = $temp_question->question_group_id;
                }
                if (property_exists($temp_question, 'showExtraInfoAndTags')) {
                    $question->showExtraInfoAndTags = $temp_question->showExtraInfoAndTags;
                } else {
                    $question->showExtraInfoAndTags = false;
                }
                if (property_exists($temp_question, 'extraInfo')) {
                    $question->extraInfo = $temp_question->extraInfo;
                } else {
                    $question->extraInfo = '';
                }
                $question->save();
            }
            /** @var SurveyTemplateQuestion $temp_question */
            foreach ($surveyTemplateQuestions as $temp_question) {
                $question = new SurveyQuestion();
                $question->question = $temp_question->question;
                $question->description = $temp_question->description;
                $question->answered_type = $temp_question->answered_type;
                $question->question_order = $temp_question->question_order;
                $question->survey_id = $step3_0_ID;

                if (property_exists($temp_question, 'question_group_id')) {
                    $question->question_group_id = $temp_question->question_group_id;
                }
                if (property_exists($temp_question, 'showExtraInfoAndTags')) {
                    $question->showExtraInfoAndTags = $temp_question->showExtraInfoAndTags;
                } else {
                    $question->showExtraInfoAndTags = false;
                }
                if (property_exists($temp_question, 'extraInfo')) {
                    $question->extraInfo = $temp_question->extraInfo;
                } else {
                    $question->extraInfo = '';
                }
                $question->save();
            }

            /** @var Simple $surveyTemplateQuestions2 */
            $surveyTemplateQuestions2 = SurveyTemplateQuestion::find(
                [
                    'conditions' => 'survey_id = ?1 ',
                    'bind' => [
                        1 => $surveyTemplate2->id
                    ]
                ]
            );

            /** @var SurveyTemplateQuestion $temp_question2 */
            foreach ($surveyTemplateQuestions2 as $temp_question2) {
                $question = new SurveyQuestion();
                $question->question = $temp_question2->question;
                $question->description = $temp_question2->description;
                $question->answered_type = $temp_question2->answered_type;
                $question->question_order = $temp_question2->question_order;
                $question->survey_id = $step3_1_ID;

                if (property_exists($temp_question, 'question_group_id')) {
                    $question->question_group_id = $temp_question->question_group_id;
                }
                if (property_exists($temp_question, 'showExtraInfoAndTags')) {
                    $question->showExtraInfoAndTags = $temp_question->showExtraInfoAndTags;
                } else {
                    $question->showExtraInfoAndTags = false;
                }
                if (property_exists($temp_question, 'extraInfo')) {
                    $question->extraInfo = $temp_question->extraInfo;
                } else {
                    $question->extraInfo = '';
                }
                $question->save();
            }


            $process = Process::findFirst(
                [
                    'conditions' => 'id = ?1',
                    'bind' => [
                        1 => $id,
                    ],
                ]
            );

            if ($process instanceof Process) {
                $process->step0 = $step0_ID;
                $process->step3_0 = $step3_0_ID;
                $process->step3_1 = $step3_1_ID;
                $process->organizationId = $organization;
                $process->save();
            }
        }
        $response = [
            'code' => 1,
            'status' => 'Success'
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function changeProcessStatus($id)
    {
        $proc = Process::findFirst(
            [
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $id
                ],
            ]
        );
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
        $data = $this->request->getJsonRawBody();

        $to_slug = $data->slug;
        $find_help_post = [
            'name' => $to_slug,
            'post_type' => 'help',
            'post_status' => 'publish'
        ];

        $help_post_result = $find_help_post;//get_posts($find_help_post);

        if (empty($help_post_result)) {
            $help_post = [
                'post_title' => $to_slug,
                'post_name' => $to_slug,
                'post_type' => 'help',
                'post_status' => 'publish'
            ];

            $help_post_id = $help_post;//wp_insert_post($help_post);

            $response = [
                'code' => 0,
                'status' => 'Success',
                'msg' => 'page not exists created just now with id: ' . $help_post_id,
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $help_post_result[0]->post_content,
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
