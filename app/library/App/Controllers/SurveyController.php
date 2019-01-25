<?php

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Services;
use App\Model\Answer;
use App\Model\OptionAnswer;
use App\Model\Process;
use App\Model\QuestionGroups;
use App\Model\SessionSubscription;
use App\Model\Subscriptions;
use App\Model\SurveyTemplate;
use App\Model\SystemicMapItems;
use App\Model\User;
use App\Traits\Auth;
use App\Traits\CheckSteps;
use App\Traits\Processes;
use App\Traits\Reports;
use App\Traits\Stats;
use App\Traits\Surveys;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use App\Model\Survey;
use App\Model\SurveyQuestion;

class SurveyController extends CrudResourceController
{
    use Auth, Surveys, CheckSteps, Stats, Processes, Reports;

    /**
     * @return mixed
     * @throws \RuntimeException
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

        $organizationId = $creator['organization']->organization_id;

        $data = $this->request->getJsonRawBody();
        $survey = new SurveyTemplate();
        $survey->title = $data->title;
        $survey->description = $data->description;
        $survey->isEditable = $data->isEditable;
        $survey->isOlset = $data->isOlset;
        $survey->creator = $creator['account']->id;
        $survey->organization_id = $organizationId;
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
     * @throws \RuntimeException
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
        $organizationId = $creator ? $creator['organization']->organization_id : null;
        $surveys = SurveyTemplate::find(
            [
                'conditions' => '	organization_id = ?1 AND isOlset = 0 ',
                'bind' => [
                    1 => $organizationId,
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
     * @throws \RuntimeException
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
        $organizationId = $creator['organization']->organization_id;
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
                    2 => $organizationId,
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
     * @throws \RuntimeException
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

        $organizationId = $creator['organization']->organization_id;

        $data = $this->request->getJsonRawBody();

        $survey = SurveyTemplate::findFirst(
            [
                'conditions' => 'id = ?1 AND organization_id = ?2 AND creator = ?3',
                'bind' => [
                    1 => $id,
                    2 => $organizationId,
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
     * @throws \RuntimeException
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

        $config = $this->getDI()->get(Services::CONFIG);

        $survey = Survey::findFirst((int)$id);

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


            $user = User::findFirst((int)$creatorId);

            $subscription = $user->getSessionSubscription() instanceof SessionSubscription
                ? $user->getSessionSubscription()->getSubscriptions()
                : 0;
            $sid = $subscription instanceof Subscriptions ? $subscription->id : 0;

            if ($process->subscription_id !== $sid) {
                throw new \RuntimeException('Your subscription haven`t access to this process');
            }

            $groups = [];
            $flag = 0;
            /** @var SurveyQuestion $item */
            foreach ($surveyQuestion as $item) {
                if ($item->question_group_id !== null && $flag < $item->question_group_id) {
                    $flag = $item->question_group_id;
                    $role = $user->role === AclRoles::ADMINISTRATOR || $user->role === AclRoles::MANAGER
                        ? AclRoles::MANAGER
                        : AclRoles::USER;
//                    $options = OptionAnswer::query()
//                        ->where('group_id = :id:')
//                        ->andWhere('role = :role:')
//                        ->bind([
//                            'id' => $item->question_group_id,
//                            'role' => $role
//                        ])
//                        ->execute();
                    $options = OptionAnswer::find([
                        'conditions' => 'group_id = :id: AND role = :role:',
                        'bind' => [
                            'id' => $item->question_group_id,
                            'role' => $role
                        ]
                    ]);
                    $groups[$item->id] = [
                        $item->id,
                        'name' => $item->getQuestionGroup()->name,
                        'options' => $options,
                        'group' => $item->question_group_id,
                        'role' => $role
                    ];
                } else {
                    $groups[$item->id] = ['id' => $item->id, 'name' => '', 'options' => []];
                }
            }

            $response = [
                'code' => 1,
                'status' => 'Success',
                'data' => $surveyQuestion,
                'groups' => $groups,
                'process' => $process,
                'isActionAAR' => !($process instanceof Process)
                    && $survey->tag !== $config->application->survey->demographics,
                'isDemographics' => $survey->tag === $config->application->survey->demographics,
                'survey' => $survey
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
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function createAnswer($id)
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);

        $data = $this->request->getJsonRawBody();

        $survey = Survey::findFirst((int)$id);

        if (!($survey instanceof Survey)) {
            throw new \RuntimeException('Survey not found');
        }

        switch ($survey->tag) {
            case '_IS_':
                $count = $config->application->survey->initCount;
                break;
            case '_CS_':
                $count = $config->application->survey->realityCount;
                break;
            case '_VS_':
                $count = $config->application->survey->visionCount;
                break;
            case '_AAR_':
                $count = $config->application->survey->aarCount;
                break;
            case '_ES_':
                $count = $config->application->survey->evaluationCount;
                break;
            case '_DS_':
                $count = $config->application->survey->demographicsCount;
                break;
            default:
                throw new \RuntimeException('Survey type not recognized');
        }

//        if ($survey->tag !== '_AAR_') {
        try {
            $answers = $this->_getSurveysAnswers($survey, $count, $survey->tag);
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'This survey has been evaluated ' . serialize($answers)
            ];
            return $this->createArrayResponse($response, 'data');
        } catch (\RuntimeException $exception) {
            //
        }
//        }

//        $config = $this->getDI()->get(Services::CONFIG)->application->survey;

        foreach ($data as $answer) {
            $oldAnswer = Answer::findFirst([
                'conditions' => 'questionId = ?1 AND userId = ?2',
                'bind' => [
                    1 => $answer->questionId,
                    2 => $this->getAuthenticatedId()
                ],
            ]);
//            if (\in_array($survey->tag, [$config->evaluation, $config->aar], true)) {
//                $answerModel = new Answer();
//            } else {
//                $answerModel = $oldAnswer instanceof Answer ? $oldAnswer : new Answer();
//            }

            $answerModel = $oldAnswer instanceof Answer ? $oldAnswer : new Answer();

            $answerModel->answer = $answer->answer;
            $answerModel->userId = $this->getAuthenticatedId();
            $answerModel->questionId = $answer->questionId;
            $answerModel->save();
        }

        if ($survey->tag === $config->application->survey->evaluation) {
            $this->createSingleReport();
            $this->createGroupReport();
        }


        $response = [
            'code' => 1,
            'status' => 'Success'];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
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


        $organizationId = $creator['organization']->organization_id;


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
                $this->extra_info = 'Process = ' . $this->processId . ' Initial';
                $step0_ID = $this->createInitSurvey();
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
            $process->creator_id = $this->getAuthenticatedId();
            $process->organizationId = $organizationId;
            if ($process->save()) {
                $this->hasInitialEvaluated($process);
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
        $action = SystemicMapItems::findFirst((int)$id);

        if (!($action instanceof SystemicMapItems)) {
            $response = [
                'code' => 0,
                'status' => 'Error',
                'data' => 'Action not found'
            ];
            return $this->createArrayResponse($response, 'data');
        }

        try {
            $this->extra_info = 'Action id = ' . $action->id . ' After Action Review for actions';
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
        $proc = Process::findFirst((int)$id);

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
     * @throws \RuntimeException
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
        $sql = 'SELECT questionId, question, answer, question_order, survey_id, userId FROM survey S '
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
        //tmp fix for now witch switch case
        switch ($to_slug) {
            case 'help_subject_glossary':
                $string = "
<p><strong>Leadership  Practices' Definitions:</strong> </p>
<p><strong>Developing  vision, strategy and policies</strong>.  This includes practices such as grounding the company vision for global responsibility  in its context, crafting a strategy that focuses on the triple bottom line, and  developing specific policies that support strategy and vision. The practice of  creating a vision helps order and prioritize the many potential activities a  company might undertake. Once a compelling vision for global responsibility is  developed, strategies are crafted to work toward that vision. The development  of specific policies links vision and strategy to organizational systems and  day-to-day operations. To become a learning organisation, a company develops a  long-term vision that is rooted in the background of the firm and takes into  account the business's strengths.</p>
<p><strong>Operationalizing  OL.</strong>This cluster covers organizational  practices that make global responsibility and OL an integral part of the  everyday practices (processes, procedures, tools and actions) of the  organization.
These practices are found in all the organization's functions,  business lines, locations. This does not mean that practices are uniform across  the organization. The organization will face different challenges in its  functions, different locations and business lines and these will need to be  addressed by suitably tailored practices. However, the expectations is that  these practices will be brought together under the umbrella of the  organization's vision, culture or overall strategy, policies and principles by  which it works. In this way locally appropriate practices are unified under  this higher level organizational umbrella. This leads to the idea that OL  principles include global responsibilities that often have different local  applications. For example, the organization will have principles by which it  operates and it is left to local employees to determine what the application of  those principles means in their area of work and to justify their actions  according to those principles.</p>
<p><strong>Top  management support.</strong>In order for a company to make  real progress toward OL and global responsibility, top management support is  vital, in different forms (e.g. adequate resourcing for projects, creation of  dedicated positions, specific investment decisions, etc.). Top management  support is revealed in actions that create visibility and awareness of OL and  global responsibility inside and outside the company, such as briefings,  executive speeches, internal newsletter or celebrations. Top management's  support, consistently endorsing the organization's OL and global responsibility  efforts, prove particularly relevant when challenges arise, during periods of  increased cost or decreased revenues, when there are difficulties with  sustained stakeholder engagement, or situations of gaining client acceptance of  CSR and OL orientation and related policies.</p>
<p><strong>Engaging  across boundaries (stakeholder engagement).</strong>
The  leadership practices in this cluster are concerned with the engagement of  stakeholders � internal as well as external � across boundaries. 
This requires  leadership practices directed to working across, e.g. personal boundaries of  social identity, internal bounda- ries of level and function, and structural  boundaries of organization, country or region. 
In the same cluster are  leadership practices geared toward developing the culture and systems to  approach stakeholders' engagement built through partnerships at any level (e.g.  with direct reports and teams, top management, clients and customers, suppliers  or the society as a whole), actively reaching out to the external community,  building balanced relationships with a clearly stated and understood  reciprocity of commitments, responsibilities and benefits, and most important  of all creating a shared understanding of the situation.</p>
<p><strong>Inclusive  and empowerment approaches.</strong>Leadership  practices in this cluster refer to actions and processes that place emphasis on  the empowerment of employees and the constructive engagement with external  stakeholders.
In this way employees and external actors are seen as a source of  knowledge, and innovation and as potential collaborators in future actions.  Experience shows that globally responsible organizations are open to learning  from whatever source it originates and that they are prepared to question  established assumptions held in the organization. Openness to learning is based  on an approach to others actors (internal and external) based on mutual  respect, participation and strong communication. Formal training for global  responsibility and OL promotes understanding of, and commitment to, the goal of  seeking solutions that provide for the financial, environmental and social  performance of the organization in the different settings in which it operates.  Training can be either provided as a stand- alone activity or is an integrated  part of standard training activities. Other leadership practices that prove  supportive and effective are mentoring and coaching, offering challenging  assignments that link business activities with performance in financial,  environmental and social terms. It is also supported by the development of  multi-functional teams which work on the actions of the organization and its  projects. Again these decentralized activities are always in line with the  principles that the organization has declared it operates.</p>
<p><strong>Communication  for OL.</strong>Leadership practices for  communicating around global responsibility and OL comprise the development of  policies and procedures to effectively collect and share information.
This  includes business needs, successes and challenges in company-wide global  responsibility as well as specific applications and actions throughout the  organization and across organizational boundaries. Communication on global  responsibility and OL follows paths and mechanisms that portray it as highly  meaningful and strategically important to the organization. It is important to  note that regular communication is more important than frequent communication.  Systematic communication includes both formal and informal communication  activities, verbal and written, adapted to fit the local context of the  audience, as well as a two-way process across organizational boundaries to inform  the company's direction for global responsibility and OL and to ensure  commitment and create a deeply shared understanding among all the different  stakeholder groups.</p>
<p><strong>Performance  development and accountability.</strong> This  means managing performance to encourage global responsibility and OL efforts  and holding individuals and groups accountable for their contributions, thus  establishing responsibility and learning goals, standards and norms at  individual and organizational levels. Elements of responsibility and learning  goals are included in employees' performance development plans. On a collective  level, companies promote external audits, look for and provide timely feedback,  and actively participate in setting standards and norms for the sector. The  formal measures contribute to monitoring and controlling performance and to  planning ahead for future goals. Managing performance to enhance global  responsibility and OL in a company implies a focus on continuous improvement  rather than the aim of meeting a certain standard and then maintaining it.  Accountability is necessary to ensure that OL goals are not only set but  enacted. Accountability at the individual level is ensured by incorporating  responsibility and learning targets in annual performance reviews, feedback  sessions, regular reporting, professional development and certifications, as  well as rewards and recognition. At the organisational level, external audits  and regular reporting help ensure accountability.</p>
<p><strong>Ethical actions.</strong>Leadership practices are here based on the  recognition that ethics and integrity are the foundations of leaders'  decision-making. Acting with integrity sets an example inside and outside the  company that shows global responsibility and OL are taken seriously. In turn,  this speeds up the integration of global responsibility and OL into the  company's business model. Managers who openly practice sustainability in their  personal life are role models for the employees. Companies set incentives for  all employees to bridge personal and professional sustainability. Acting  ethically and with integrity is fundamental at the organizational level, e.g.  participative decision-making including the major stakeholders, the use of  decision criteria that include environmental, social, and financial considerations,  open book practices and systematic integrity policies for suppliers and clients  are practices that represent consistency and honesty on a collective  level. </p>

";
                break;
            case 'help_subject_step-2-design-and-implement':
                $string = "
<p>For Organizational Learning  to be effective, diversity, inclusiveness, empowerment, self-organization, and  free flow of people, information and resources, are necessary characteristics  of the active organizational culture. A strong and positive organizational  culture, especially the type described here, is not easy to be created.  However, the following tool, if used as an approach to decision making, holds  the potential to create and sustain it efficiently and effectively, besides its  value as a tool for project design action lists.</p>
<p> Taking the set of  insights regarding potential change projects generatedduring Step 1, this  is the stage for their detailed exploration. Ideally, the whole set of insights  would be acted upon however time restrictions naturally impose prioritization.  This prioritization is recommended to be based upon careful balance between  level of intervention (i.e. fundamental or superficial, systemic or events  level) and the 'quick-wins' best practice. Priority is recommended to be given  to insights that can bring quick-wins while intervening at the fundamental  systemic level of the organisation.</p>
<p> Each one of the items  derived from Step 1 will be in a generic form, e.g. 'sales-products', and  should be transformed into a specific potential project, e.g. 'double the sales  of X product'. Generating several specific potential projects from each of the  items is recommended. For example, for the same item an additional  clarification could be to 'introduce an innovative product X on the market that  will attract x% of the market share', while for prioritization the same  criterion as above applies.</p>
<p> Then, participants  explore each of the chosen projects by assuming the task of retrospection  during a hypothetical future debriefing meeting. Imagining that future meeting,  the desired end result of a project is phrased as a failure inquiry question on  a past tense and a negative way which is then recordedat the center of a  paper, e.g. 'why did sales of product X not double during the last year?', 'why  wasnew product X not launched last year?' and 'why did new product X not  attract x% of the market share two years after its launch?' Note that in the  second example there are two potential reasons for project failure that need to  be inquired into separately in order for each reason to reveal the whole set of  causes, and thus the tasks to be undertaken in the present. Using imagination  and past experience, the users record potential causes of this imagined failure  around the central item, as well as causes of the causes as in the example. The  sum of the causes recorded on the map constitutes a reverse and yet detailed  action plan for the project that is recorded as a hypothetical failure at the  center of the map. Implementation of these projects based on these action plans  through inclusive and empowering approaches (see Glossary of terms) is the  final phase of Step 2.</p>
<p> In case the answer  validity ground rule is violated by recording an opinion instead of an  observable fact on the map, users will soon discover a natural elimination process  since opinions provide no insights for specific actions. For example, 'because  our sales force was not competent enough' offers only discouragement by  focusing on the titanic and bluer goal of creating a highly competent sales  force. In contrast, a statement like 'because our sales force was not trained  to the relevant best practices in sales' is an observable fact, since perhaps  sales best practices training was provided by the organisation's competitors  and/or partners to their employees, clearly indicating a specific action � i.e.  provide best practices training to the organisation's sales force.</p>
<p> Just click the big blue button under the  Step 2 section of the process page that in named Systemic Action Map (SAM). The  sum of the projects that you came up with during Step 1 exists as a SAM. Browse  and choose the one you want to work with. The central item is already in place.  Click the + button on the left list to add actions. Just answer the question  that appears at the pop up window. After finishing the actions, which are  actions that can be implemented immediately, press the Create SAM Report  button. Your Action list appears at the Report in the form of an xls file that  you can print and use. </p>
";
                break;
            case 'help_subject_step-3-self-evaluate':
                $string = "
<p>The last step of each  cycle is self-evaluation, which forms the link between strategy and  performance.&nbsp;Any key performance indicator can be used at this stage, but  OLSET provides two ways of self-evaluation � both of which need to be used to  secure an OL strategy.</p>
<p> On a daily basis Step  3 is performed through the tool named After Action Review (AAR) and is placed  as the last big blue button at the end of the Process page named After Action  Review for Actions. AAR is a sequence of four questions intended for use right  after the conclusion of each individual action/project, practically enacting  and forcing double-loop learning. Click the big blue button to fill in an AAR.  Decide if you want to reflect on an Action that exists at your OLSET Task List  OR create an AAR for a non recorded action. To browse the recorded actions type  the first letters and pick the right one from the menu that appears. Fill in  the four questions of the AAR mindfully.&nbsp;</p>
<p> At the end of the  OLSET process, the self-evaluation questionnaire provides a way to evaluate the  progress of your group or organisation over time. The 28-item OLSET  questionnaire provides a holistic measurement and thus picture of the  organisation, department, team, etc.</p>
<p> The evaluation process  can be either direct, i.e. one meeting where all stakeholders are present to  fill in the questionnaire and discuss the results, or indirect, i.e. each  stakeholder fills in the questionnaire privately. Then, the mean scores for  each question are calculated and the information fed back to all users,  providing in this way a clear direction and valuable insights to be used as  input for Step 0 of the new cycle.</p>
<p> At the process page roll down to Step 3.  Click the big blue button that says Evaluation Survey. A Participants'  Information appears with instructions for filling in the evaluation survey as  well as anonymity confirmation. Read carefully, accept and move on to the  survey. Fill in the survey. After all users have filled in the survey go to the  Dashboard and click Group Evaluation Report. Read the report carefully and if  possible do the exercise of answering questions that exists at the end of the  Report to further reflect on each aspect of your OL function. The most  important metric, the OLSET Index's progress over time is also presented at the  Dashboard.&nbsp;</p>
<p>Click the After Action Review for the  OLSET Process you just completed. Fill in the questions and reflect on how you  could improve your OLSET process next time.&nbsp;</p>
";
                break;
            case 'help_subject_step-0-reflect':
                $string = "
<p>This is the first part  of the OL management process, wherein the user assumes an unattached observer's  role, intended to neutralize the mental models that act as a filter of the  reality, taking detailed stock of the basics, i.e. current reality and the  vision of the situation at hand (e.g. process, project, department, unit, whole  organisation).</p>
<p> Step 0 is for  observation and reflection. The user, assuming the role of observing a friend's  organisation, department, team, process, employee or problem, takes a  metaphorical step back, dissolving any attachment with the situation at hand  and enabling objectivity in the inquiry into the current reality and vision.  Inquire by answering the questions of the Current Reality Survey and the Vision  Survey. After both are clear and the same for the whole team, the administrator  should fill in the text boxes of Current Reality and Vision.</p>
<p> Both the identified  current reality and the vision should be recorded and readily accessible to all  OLSET users at the respective text boxes at the process page, as this information  composes the clear path ahead for the learning and development of the  organisation. The vision constitutes the major milestone against which the  learning and development need to be benchmarked at the end of every cycle. The  Vision and the Current Reality also appear at the Dashboard</p>
<p> In other words: Tell  (rethink) your story. This step should help you open your mind and gather  information. What if you were a by-stander who looks at your organisation from  an objective perspective? Let go of the irrelevant information. Let go of your  assumptions, your expectations or hopes for the future. Open yourself to what  your story wants you to understand.<br>
Step 0 is for  assessing the current state of the system of interest as well as its envisioned  one. Neither the causes behind the current state nor the way to achieve the  vision and whether it is feasible, realistic etc. are of interest at this  stage.</p>
<p> Initial Intentions (or Strategic  Intent):</p>
<p>The administrator of the team fills in  the Initial Intentions text box at the beginning of the process page consulting  the managers of the team.</p>
<p>Ask mind-opening, heart-opening and  hand-opening questions.<br>
Step out of the  system.<br>
What do you want to  achieve?<br>
What is your  goal?<br>
What would make you  intensely happy?<br>
Define: what it is  that you want to create with this system?</p>

";
                break;
            case 'help_subject_step-1-understand':
                $string = "
<p>At this stage, in possession of a clear picture of both the situation at hand and the intended end result, the user steps into the action cycle seeking to understand the system of intervention. The system's 'systemic structure' (i.e. stocks and flows or in other words components and their interconnections, among the various elements of the organisation/ department/operation/team/process) is the inquiry and aim of this stage. This stage answers questions like: how does the system work? how is it usually treated? </p>
<p>and what would be the best way to treat it now in order to maximize the chances of reaching the envisioned state?
What drives the system's behavior and defines the patterns of behaviour that then define the events is the systemic structure.
The systemic structure was created when the system was first put in place and its defining factors (components) were a) the mental models (or assumptions) of the individuals that created it and b) the artifacts (scientific and technological knowledge, measurement systems, etc.) of the time. In practice, any new process/product/team/etc. implanted into a non-altered systemic structure will produce the same results over time. Consequently, the purpose of Step 1 is to acquire a clear picture of the systemic structure and its dynamics. </p>
<p>'When placed in the same system, people, how- ever different, tend to produce similar results' (Senge, 1990: 44)
'System structure is the source of system behavior. System behavior reveals itself as a series of events over time.' (Meadows, 2009: 89)
Define the most important variables. Deepening your rationality. Search for the key elements that become more ore less in your story. Define the variables neutral. What is increasing, what is decreasing? What examples of delay are you experiencing? What patterns do you observe?
Your vision is automatically placed as the centre of your system. Then, by adding the + button at the list that appears at the left of the map you add items. Answer the question at the window that opens about what key elements of your current reality can increase or decrease.</p>
<p> The item is automatically placed at its proper position. Define also the value of the impact: does this item increase (add more to) your vision or decrease (results in your vision being less)? The values are then placed automatically. Press again the + sign at the list on your left and answer the question regarding what intervention projects you can do for each key element that impacts your vision. Add the more/less values. After finishing this process for all your key elements then click the Create SSM Report button. Read the pdf report that comes out and decide the prioritisation of your projects. To help you in this we have questions that you can use at the end of the Report. </p>

";
                break;
            case 'help_subject_ol-measurement':
                $string = "
<p>This survey contains  28 statements regarding the Organizational Learning capacity of your  organisation, which approximately can be filled out in 10 minutes.</p>
<p> The possible answers  range from 1 to5 indicating your level of agreement with each of the  statements. The range goes from 1: Totally Disagree (indicating that the  statement is never true) to 5: Totally Agree (indicating that the statement is  always true). Pleaseselect the answer that better fits your current  experience in your organisation and make sure you respond to all the questions.  Furthermore, sometimes there are somedemographic questions regarding  information on the type and form of your organisation at the beginning.</p>
<p> All the information  that you provide us willremain anonymous and we will never share your  data.</p>
<h3>OL Evaluation Report:</h3>
<p>Please read carefully the report  provided in this stage. Make sure to do the reflective exercise at the end of  the report. The Evaluation Report can be accessed by clicking the respective  buttons that exist at the centre of the Dashboard.&nbsp;</p>
respective buttons that exist at the centre of the Dashboard. 
";
                break;
            case 'help_subject_ol-measurement-intro':
                $string = "
<p>To what extent do you  believe that your organization is learning?<br>
In our days when the  pace of change is getting faster and faster, it is the capability of learning  that becomes the key to our survival and sustainable success. This applies both  to individuals as well as to organizations. Based on former research a new tool  has been developed to measure organizations' ability to adapt to change or in  other words to measure their Learning Organization or Organizational Learning  capacity.</p>
<p> Based on your answers  to the following questions &nbsp;the level of Organizational Learning (OL)  achieved so far in your company can be ascertained. OL is also related to the  sustainability of an organization and thus this questionnaire will also indicate  how sustainable your organization is at the specific point in time.<br>
The self-evaluation  questionnaire is composed of 28 descriptive statements to be answered in terms  of your level of agreement, from Completely Disagree to Completely  Agree.&nbsp;The questionnaires should be anonymous for unbiased  results.&nbsp;The questionnaire measures the OL capacity of the group that uses  it. It can therefore be used by any team and/ or the whole organisation to  measure and improve performance, sustainability and all the other beneficial  implications that are associated with managing OL.</p>
<p> Ideally, anyone  related to the project, team and/or organisation that is being measured should  fill in this questionnaire. This means the whole workforce must answer when  measuring a whole organization's OL capacity. However, you can adjust this and  choose to measure, for example, only the OL for the team of managers of your  organisation or a statistically strong sample like 10 people from each  department.</p>
<p> If you wish, you can  also separately calculate the OL capacity of different groups of employees,  such as office clerks, managers, technical staff, etc. In such a case you  should add a demographic question at the beginning of the questionnaire to  determine which group the survey participant belongs to.</p>

";
                break;
            case 'help_subject_cycle-intro':
                $string = "
<p>OLSET allows you to  access the benefits of the contemporary participative management method, namely  the bottom-up approach, without sacrificing efficiency and thus effectively and  without risk in large- scale applications. OLSET provides tools for your  decision-making process wherein OL and strategy are already integrated, and  thus can bring forth considerably improved bottom line results.</p>
<p> OLSET is a simple cyclical  reflection process for self-organized decision-making and project management,  no matter how big or small the project or organisation is. The process is  composed of the following iterative steps: &quot;Reflect&quot;,  &quot;Understand&quot;, &quot;Design and Implement&quot; and &quot;Evaluate&quot;.</p>
<p> OLSET has three ground  rules that apply to all and any question and inquiry techniques employed in it:</p>
<p><strong>A.  Answer validity:</strong>Valid answers, which are  consequently taken under consideration and utilized within the process, are  those based on observable facts. Searching for answers based on observable  facts generates a variety of responses, all of which need to be included in the  inquiry process, producing a spectrum of causes to be tackled. Answer Validity  example: Why did the organisation not meet last year's sales targets? An  answer like 'because our sales department/team/employees are not good  enough/incompetent/not committed/not interested etc.' is an opinion, and is  thus not considered valid and consequently not included and recorded as an  answer. Most probably, answers based on observable facts, i.e. that are valid  and recordable, will come primarily from individuals of the sales department  that are directly related to the issue at hand. If individuals of at least  three different hierarchical levels of the respective  department /operation/process are not present in the room to provide valid  answers, they must be located and asked. Valid answers will include observable  facts (e.g. 'because our sales force is not numerous enough', 'because our  price is not competitive enough' or 'because our product does not satisfy  customers enough') and will be based on observable data (e.g. 'the targets were  set based on the conditions and revenue of the previous year but in the  meantime the sales department's staff numbers were reduced', 'our competitors  have a lower price point for a similar product' or 'a new and better product  was launched in the market', respectively).</p>
<p><strong>B.  Answer depth:</strong>All questions need to be asked  repeatedly until all levels of potential answers are explored and the root  cause(s) is/are revealed, like peeling an onion. This process will provide a  great spectrum of valid answers on multiple levels, all of which need to be  encompassed in the subsequent steps of the process. Answer Depth example: In  the above example where the question &quot;Why did the organisation not meet  last year's sales targets?&quot; is posed, the first time that this question  was posed, within the spectrum of answers there was one indicating 'because our  sales force is not numerous enough'. Posing the question for a second time in  the reverse form of 'why was our sales force not numerous enough to meet last  year's sales targets?' generates an answer one level deeper, that is 'the  targets were set based on the conditions and sales of the previous year but in  the meantime the sales department's staff numbers were reduced'. </p>
<p>The question  then needs to be posed again, adjusting it once more to inquire into the cause  of the new answer, e.g. 'why was the target not adjusted to reflect the new  number of salespeople so that it could have been met?' Once again a spectrum of  potential answers will be generated and the valid ones need to be recorded and  investigated. For example, 'the accounting department did not make the target  adjustment', 'Why did the accounting department not make the adjustment so that  the sales target would had been met?' now, according to the answer breadth  ground rule, three people from three levels of the accounting department's  hierarchy need to be asked. The question needs to be posed again and again to  these people until root causes that clearly indicate a necessary practical  change/action will be revealed, e.g. a missing/broken information flow process  from the HR department to the accounting one that needs to be restored, an  assumption on the part of top management regarding the reduced sales force's  capacity to meet the targets that needs to be reconsidered, the effectiveness  of a performance enhancement training/project employed last year to bridge the  gap of the reduced sales force that needs to be reevaluated, etc.</p>
<p><strong>C.  Answer breadth:</strong>Answer breadth is defined by the  number of people as well as by the variety of hier archical levels who provide  answers to a question. Ideally, at least three people from at least three different  hierarchical levels will provide their answers to every question. The synthesis  of all the answers is the answer that should be utilized within the process.<br>
The starting point  should always be the leader of the user-group himself/herself, followed by a  subgroup of the user-group if any (for example, a single department of an  organization). Subsequently, the methodology should be encouraged to grow at a  fast pace in other existing subgroups. Last, the whole group or organisation  should be engaged. The pace of this methodology can be diverse depending on the  function; for example, operations will go through the cycle fast and frequently  while the strategic level might go through one cycle every year.</p>

";
                break;
            case 'help_subject_xxx':
                $string = '';
                break;
            default:
                /*
                $string = 'We are sorry but this functionality is still under development.
Please check out the <a href="/help/subjects">Help</a> section and/or contact our 24/7 Helpdesk through
<a href="mailto:info@olset.org">info@olset.org</a>';
                */
                $string = '<p>Welcome to OLSET!&nbsp;<br>
  Our goal is to help you achieve  your vision fast and easy.</p>
<p>As soon as you log in a pop up  window appears for you to choose the subscription of yours that you want to  work with. Choose from the drop down menu the Free subscription, the  single-user subscription or your group subscription.&nbsp;</p>
<p>If this is the first time you use  OLSET then you will be called to measure the Organisational Learning capacity  of your organisation at this point in time. A participant\'s Information Sheep  appears where you have to tick Agree, then you are brought into the page where  you need to fill in your demographic data and right after that the measurement  survey of 28 questions appears. Fill in the survey. Then go to Dashboard from  the left menu. At the centre of the Dashboard you can click the Single  Evaluation Report to read about your results or if you have a group  subscription, as soon as all your team members fill in the survey, click Group  Evaluation Report to receive your results. An exercise exists at the end of the  report that can help you improve each one of your results.&nbsp;</p>
<p>After the Initial Evaluation  survey, click OLSET Process at the left menu to get to the Process page and you  go to Step 0. Click the Current Reality Survey and fill it in. If you are an  administrator you will be called to synthesise your survey answers at the  Current Reality Text box. Fill it in and click Save. Then click and fill in the  Vision survey. Again the administrator is called to fill in also the Vision  text box synthesising the answers of the Vision survey. Click Save. Fill in  also the&nbsp; Initial Intentions text box.&nbsp;</p>
<p>Then in Step 1, click the Systemic  Structure Map (SSM) button. An SSM of your vision is ready for you to fill in.  Click the + sign to add items. Fill in the answers at the window that opens and  click Add. When you reach the level of Projects and fill in all the  intervention projects that you can do the SSM is ready. Go back to the list of  SSM and click the last button showing a list to generate and receive your SSM  Report. Again there is an exercise at the end of the report for you to dive  deeper. Prioritise your intervention projects using the insights of the report.  When you choose which one you want to work with first click the OLSET Process  button to get back into the Process page.&nbsp;</p>
<p>At the Process page click the  Systemic Action Map (SAM) of Step 2. A list of SAMs appears. Click on the one  you have chosen. Item 1, your intervention project is already there. Click the  + sign and add items by answering the questions that appear at the pop up  window. When you finish click the Back button at the top right menu to go back  to the list of SAMs. Click the last button that shows a list. Your Action List  appears as an xls which you can print out. Do the same for the next SAM you  have chosen until you receive all the Action Lists that you want.&nbsp;</p>
<p>After you complete every action go  to Step 3 at the Process page and at the After Action Review for Actions click  the After Action Review (AAR). On the window that appears choose if you want an  existing in your lists action or if you are doing an AAR for a non existing  action. Fill in the four questions\' survey that appears. Do this for every  single action in your lists.&nbsp;</p>
<p>As soon as you complete one  project go again at Step 3 of the Process Page and fill in first the Evaluation  Survey. Get your Evaluation Report from the Dashboard like you did for the  Initial measurement. Then click on the After Action Review for Process and fill  in the AAR for the OLSET process you just completed. Do this step every time  you finish one of your intervention projects.&nbsp;</p>
<p>Welcome to your new envisioned  reality!&nbsp;</p>
';
        }

        //string char  fix
        $string = str_replace('�', '-', $string);

        $response = [
            'code' => 1,
            'status' => 'Success',
            'data' => $string,
        ];


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
//        $response = [
//            'code' => 1,
//            'status' => 'Success',
//            'data' => '<div>There will be<br> help data</div>',
//        ];
        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     * @throws \RuntimeException
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
        /** @var User $user */
        $user = $this->getAuthenticated();

        $subscription = $user->getSessionSubscription() instanceof SessionSubscription
            ? $user->getSessionSubscription()->getSubscriptions()
            : 0;

        $sid = $subscription instanceof Subscriptions ? $subscription->id : 0;

        $sql_getProcesses = 'SELECT PR.id, PR.title, PR.`step0`, PR.`step3_0`, PR.`step3_1`,'
            . ' PR.`reality`, PR.`vision`, PR.subscription_id FROM `process` PR '
            . ' INNER JOIN survey S ON PR.`step0`= S.id OR PR.`step3_0`= S.id OR PR.`step3_1`= S.id '
            . ' OR PR.`reality`= S.id OR PR.`vision`= S.id '
            . ' WHERE PR.subscription_id IS NOT NULL AND PR.subscription_id = ' . $sid
            . ' AND PR.id IN (SELECT  `processId` FROM `process_departments` WHERE `departmentId` '
            . ' IN (SELECT department_id FROM user_department WHERE user_id =  ' . $creatorId
            . ' )) OR PR.subscription_id IS NOT NULL AND PR.subscription_id = ' . $sid
            . ' AND PR.id IN (SELECT  `processId` FROM `process_organizations` WHERE `organizationId` '
            . ' IN (SELECT organization_id FROM user_organization WHERE user_id =  '
            . $creatorId . ' )) OR PR.subscription_id IS NOT NULL AND PR.subscription_id = ' . $sid
            . ' AND PR.id IN (SELECT `processId` FROM `process_users` WHERE userId = ' . $creatorId . ' ) '
            . ' GROUP BY PR.`step0`, PR.`step3_0`, PR.`step3_1`, PR.`reality`, PR.`vision`, PR.`id`';


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

            $sql_isCompleted_reality = 'SELECT count(A.id) as countAnswers,S.title FROM `answers` A '
                . 'INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
                . 'INNER JOIN survey S ON SQ.survey_id = S.id  WHERE A.userId ='
                . $config->application->admin . ' AND SQ.survey_id = '
                . $val['reality'] . ';';
            $data_isCompleted_reality = $connection->query($sql_isCompleted_reality);
            $data_isCompleted_reality->setFetchMode(Db::FETCH_ASSOC);
            $iresults_isCompleted_reality = $data_isCompleted_reality->fetchAll();

            $sql_isCompleted_vision = 'SELECT count(A.id) as countAnswers,S.title FROM `answers` A '
                . 'INNER JOIN survey_questions SQ ON A.questionId = SQ.id '
                . 'INNER JOIN survey S ON SQ.survey_id = S.id  WHERE A.userId ='
                . $config->application->admin . ' AND SQ.survey_id = '
                . $val['vision'] . ';';
            $data_isCompleted_vision = $connection->query($sql_isCompleted_vision);
            $data_isCompleted_vision->setFetchMode(Db::FETCH_ASSOC);
            $iresults_isCompleted_vision = $data_isCompleted_vision->fetchAll();

            if ($user instanceof User) {
                $demographicsSurvey = $this->getDemographicsSurvey($user);
            } else {
                throw new \RuntimeException('User not authenticated');
            }

            $process = Process::findFirst((int)$val['id']);
            if (!$this->processFinished($process)) {
                continue;
            }

            $processes[] = [
                'processId' => $val['id'],
                'process_title' => $val['title'],
                'process' => $process,
                'index' => $this->getOlsetIndexesCompare($process),
                'user' => $this->getParticipatedUsersCompare($process),
                'absolute' => $this->getAbsoluteOlsetIndexCompare($process),
                'previousIndex' => $this->previousIndex,
                'lastIndex' => $this->lastIndex,
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
                    ],
                    'reality' => [
                        'id' => $val['reality'],
                        'title' => $iresults_isCompleted_reality[0]['title'],
                        'isCompleted' => $iresults_isCompleted_reality[0]['countAnswers'] > 0 ? 1 : 0
                    ],
                    'vision' => [
                        'id' => $val['vision'],
                        'title' => $iresults_isCompleted_vision[0]['title'],
                        'isCompleted' => $iresults_isCompleted_vision[0]['countAnswers'] > 0 ? 1 : 0
                    ],
                    'demographics' => [
                        'id' => $demographicsSurvey instanceof Survey ? $demographicsSurvey->id : null,
                        'title' => $demographicsSurvey instanceof Survey ? $demographicsSurvey->title : '',
                        'isCompleted' => $demographicsSurvey instanceof Survey
                            ? $this->getDemographicsAnswers($demographicsSurvey)
                            : false
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
