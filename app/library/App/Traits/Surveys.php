<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 06.09.18
 * Time: 17:55
 */

namespace App\Traits;

use App\Constants\Services;
use App\Model\Answer;
use App\Model\Organization;
use App\Model\Process;
use App\Model\ProcessYearSurvey;
use App\Model\Subscriptions;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\SurveyTemplate;
use App\Model\SurveyTemplateQuestion;
use App\Model\User;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;

trait Surveys
{
    protected $extra_info;

    protected $processId;


    /**
     * @param User $user
     * @return bool
     * @throws \RuntimeException
     */
    protected function createDemographicsSurvey(User $user): bool
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        $survey = Survey::findFirst([
            'conditions' => 'creator = ?1 AND tag = ?2',
            'bind' => [
                1 => $user->id,
                2 => $config->application->survey->demographics,
            ],
        ]);
        if (!($survey instanceof Survey)) {
            $this->extra_info = 'User = ' . $user->id . ' Demographics';
            $surveyTemplate = SurveyTemplate::findFirst(
                [
                    'conditions' => 'tag LIKE "%' . $config->application->survey->demographics . '%"',
                    'bind' => [
                    ],
                ]
            );
            return $this->_createSurvey($surveyTemplate);
        }

        return true;
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function createEvaluationSurvey(): int
    {
        if ($this->extra_info === null) {
            $this->extra_info = 'Process = ' . $this->processId . ' Evaluation';
        }
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%' . $config->application->survey->evaluation . '%"',
                'bind' => [
                ],
            ]
        );
        return $this->_createSurvey($surveyTemplate);
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function createInitSurvey(): int
    {
        if ($this->extra_info === null) {
            $this->extra_info = 'Process = ' . $this->processId . ' Evaluation';
        }
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%' . $config->application->survey->init . '%"',
                'bind' => [
                ],
            ]
        );
        return $this->_createSurvey($surveyTemplate);
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function createAfterActionReviewSurvey(): int
    {
        if ($this->extra_info === null) {
            $this->extra_info = 'Process = ' . $this->processId . ' After Action Review';
        }
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%' . $config->application->survey->aar . '%"',
                'bind' => [
                ],
            ]
        );
        return $this->_createSurvey($surveyTemplate);
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function createCurrentSituationSurvey(): int
    {
        if ($this->extra_info === null) {
            $this->extra_info = 'Process = ' . $this->processId . ' Current Situation';
        }
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%' . $config->application->survey->CRS . '%"',
                'bind' => [
                ],
            ]
        );
        return $this->_createSurvey($surveyTemplate);
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function createVisionSurvey(): int
    {
        if ($this->extra_info === null) {
            $this->extra_info = 'Process = ' . $this->processId . ' Vision';
        }
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%' . $config->application->survey->VS . '%"',
                'bind' => [
                ],
            ]
        );
        return $this->_createSurvey($surveyTemplate);
    }


    protected function createSurveyQuestion($data, $id, $template = false)
    {
        $surveyQuestion = $template ? new SurveyTemplateQuestion() : new SurveyQuestion();
        $surveyQuestion->question = $data->question;
        $surveyQuestion->description = $data->description;
        $surveyQuestion->answered_type = $data->answered_type;
        $surveyQuestion->question_order = $data->question_order;
        $surveyQuestion->question_group_id = $data->question_group_id;
        $surveyQuestion->survey_id = $id;
        return $surveyQuestion;
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function fullSurveyData($id)
    {
        $query = new Builder();
        $query->addFrom(Process::class, 'Process');
        $query->leftJoin(
            ProcessYearSurvey::class,
            '[ProcessYearSurvey].[process_id] = [Process].[id]',
            'ProcessYearSurvey'
        );
        $query->leftJoin(
            Survey::class,
            '[ProcessYearSurvey].[survey_id] = [Survey].[id]',
            'Survey'
        );
        $query->leftJoin(
            Survey::class,
            '[ProcessYearSurvey].[reality] = [Survey].[id]',
            'YearReality'
        );
        $query->leftJoin(
            Survey::class,
            '[ProcessYearSurvey].[reality] = [Survey].[id]',
            'YearVision'
        );
        $query->andWhere('[Process].[id] = :id:', ['id' => $id]);
        $query->orderBy('[ProcessYearSurvey].[date]');
        $query->columns([
            '[Process].*',
            '[ProcessYearSurvey].*',
            '[Survey].*',
            '[YearReality].*',
            '[YearVision].*',
        ]);
        return $query->getQuery()->execute();
    }

    /**
     * @param $id
     * @throws \RuntimeException
     */
    protected function initYearProcesses($id): void
    {
        /** @var Process $process */
        $process = Process::findFirst((int)$id);
        $now = new \DateTime();
        $createdDate = $process->createdAt;
        $createdDate = new \DateTime($createdDate);
        $diff = (int)$createdDate->diff($now)->y;
        if ($diff > 0) {
            /** @var Simple $yearSurveys */
            $yearSurveys = $process->getProcessYearSurvey();
            if ($yearSurveys->count() !== $diff) {
                for ($i = 0; $i < $diff; $i++) {
                    if ($i === 0) {
                        $year = new \DateTime();
                    } elseif ($i === 1) {
                        $year = (new \DateTime())->modify('-1 year');
                    } else {
                        $year = (new \DateTime())->modify('-' . $i . ' years');
                    }
                    $this->createYearSurvey($process, $year, $yearSurveys);
                }
            }
        }
    }

    /**
     * @param Process $process
     * @param \DateTime $year
     * @param Simple $yearSurveys
     * @throws \RuntimeException
     */
    protected function createYearSurvey(Process $process, \DateTime $year, Simple $yearSurveys): void
    {
        if (!$this->_checkYear($year, $yearSurveys)) {
            $yearSurvey = new ProcessYearSurvey();

            $this->extra_info = 'Year Survey Evaluation' . $year->format('Y-m-d');
            $yearSurvey->process_id = $process->id;
            $yearSurvey->survey_id = $this->createEvaluationSurvey();
            $this->extra_info = 'Year Survey CRS' . $year->format('Y-m-d');
            $yearSurvey->reality = $this->createCurrentSituationSurvey();
            $this->extra_info = 'Year Survey VS' . $year->format('Y-m-d');
            $yearSurvey->vision = $this->createVisionSurvey();
            $yearSurvey->date = $year->format('Y-m-d H:i:s');
            if (!$yearSurvey->save()) {
                $survey = Survey::findFirst($yearSurvey->survey_id);
                if ($survey instanceof Survey) {
                    $survey->delete();
                }
                $survey = Survey::findFirst($yearSurvey->vision);
                if ($survey instanceof Survey) {
                    $survey->delete();
                }
                $survey = Survey::findFirst($yearSurvey->reality);
                if ($survey instanceof Survey) {
                    $survey->delete();
                }
                throw new \RuntimeException('Year Survey not created');
            }
        }
    }

    /***************** INIT PROCESS *******************/

    /**
     * @param Process $process
     * @return bool
     * @throws \RuntimeException
     */
    protected function isFirstProcess(Process $process): bool
    {
        /** @var Organization $organization */
        $organization = $this->getAuthOrganization();

        /** @var User $user */
        $user = $this->getAuthenticated();

        $subscription = $user->getSessionSubscription()
            ? $user->getSessionSubscription()->getSubscriptions()
            : null;
        /** @var Simple $processes */
        $processes = Process::find([
            'conditions' => 'organizationId =?1 AND subscription_id = ?2 ',
            'bind' => [
                1 => $organization->id,
                2 => $subscription instanceof Subscriptions ? $subscription->id : 0
            ],
        ]);

        /** @var Process $firstProcess */
        $firstProcess = $processes->getFirst();
        return $firstProcess->id === $process->id;
    }

    /**
     * @return bool | Process
     * @throws \RuntimeException
     */
    protected function getFirstProcess()
    {
        /** @var Organization $organization */
        $organization = $this->getAuthOrganization();
        /** @var User $user */
        $user = $this->getAuthenticated();

        $subscription = $user->getSessionSubscription()
            ? $user->getSessionSubscription()->getSubscriptions()
            : null;
        /** @var Simple $processes */
        $processes = Process::find([
            'conditions' => 'organizationId =?1 AND subscription_id = ?2 ',
            'bind' => [
                1 => $organization->id,
                2 => $subscription instanceof Subscriptions ? $subscription->id : 0
            ],
        ]);
        /** @var Process $firstProcess */
        return $processes->getFirst();
    }

    /**
     * @param Process $process
     * @param bool $fill
     * @return bool
     * @throws \RuntimeException
     */
    protected function hasInitialEvaluated(Process $process, $fill = true): bool
    {
        $initialSurveys = $this->getInitialSurveyAnswersByUser();
        $processInitialSurveyAnswers = [];
        try {
            $processInitialSurveyAnswers = $this->getInitialSurveyAnswersByProcess($process);
        } catch (\RuntimeException $exception) {
            //
        }


        if (\count($processInitialSurveyAnswers) > 0) {
            return true;
        }

        /** @var User $user */
        $user = $this->getAuthenticated();

        $subscription = $user->getSessionSubscription()
            ? $user->getSessionSubscription()->getSubscriptions()
            : null;
        $sid = $subscription instanceof Subscriptions ? $subscription->id : 0;


        if (\count($initialSurveys) > 0 && \count($processInitialSurveyAnswers) === 0) {
            /** @var Survey $survey */
            $survey = $initialSurveys[0]['survey'];
            $initialProcess =  $survey->getProcess0();
            if ($initialProcess->subscription_id !== $sid) {
                return false;
//                throw new \RuntimeException('Your subscription haven`t access to this process');
            }
            if ($initialProcess instanceof Process && $process->id === $initialProcess->id) {
                return true;
            }
            $answers = $initialSurveys[0]['answers'];
            if ($fill) {
                /** @var Simple $answers */
                $this->addInitialSurveyAnswers($process, $answers);
                return true;
            }
            return true;
        }
        return false;
    }

    /**
     * @param Process $process
     * @param iterable $answers
     * @throws \RuntimeException
     */
    private function addInitialSurveyAnswers(Process $process, iterable $answers): void
    {
        $initial = $process->getSurveyInitial();
        /** @var Simple $questions */
        $questions = $initial->getSurveyQuestions();
        $newAnswers = [];
        try {
            /** @var Answer $answer */
            foreach ($answers as $answer) {
                $question = $answer->getSurveyQuestions();
                /** @var SurveyQuestion $model */
                foreach ($questions as $model) {
                    if ($question->question === $model->question) {
                        $newAnswer = new Answer();
                        $newAnswer->questionId = $model->id;
                        $newAnswer->answer = $answer->answer;
                        $newAnswer->userId = $answer->userId;
                        $newAnswers[] = $newAnswer;
                    }
                }
            }
        } catch (\RuntimeException $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
        $answer = null;
        unset($answer);
        foreach ($newAnswers as $answer) {
            $answer->save();
        }
    }

    /**
     * @param Process $process
     * @return array
     * @throws \RuntimeException
     */
    protected function getInitialSurveyAnswersByProcess(Process $process): array
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        /** @var Survey $initialSurvey */
        $initialSurvey = $process->getSurveyInitial();
        if ($initialSurvey instanceof Survey) {
            return $this->_getSurveysAnswers($initialSurvey, $config->application->survey->initCount, 'Initial');
        }
        throw new \RuntimeException('Initial survey not found App/Traits/Surveys.php:416');
    }

    /**
     * @param bool $answer
     * @param bool $survey
     * @return array
     * @throws \RuntimeException
     */
    protected function getInitialSurveyAnswersByUser($answer = true, $survey = true): array
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        /** @var Organization $organization */
        $organization = $this->getAuthOrganization();
        /** @var User $user */
        $user = $this->getAuthenticated();

        $subscription = $user->getSessionSubscription()
            ? $user->getSessionSubscription()->getSubscriptions()
            : null;
        /** @var Simple $processes */
        $processes = Process::find([
            'conditions' => 'organizationId =?1 AND subscription_id = ?2 ',
            'bind' => [
                1 => $organization->id,
                2 => $subscription instanceof Subscriptions ? $subscription->id : 0
            ],
        ]);
        $initialSurveys = [];
        /** @var Process $process */
        foreach ($processes as $process) {
            $initialSurvey = $process->getSurveyInitial();
            try {
                $answers = $this->_getSurveysAnswers(
                    $initialSurvey,
                    $config->application->survey->initCount,
                    'Initial'
                );
                if ($answer && $survey) {
                    $initialSurveys[] = ['survey' => $initialSurvey, 'answers' => $answers];
                } elseif ($answer) {
                    $initialSurveys[] = $answers;
                } else {
                    $initialSurveys[] = $initialSurvey;
                }
            } catch (\RuntimeException $exception) {
                continue;
            }
        }
        return $initialSurveys;
    }


    /****************  PRIVATE  **********************/

    /**
     * @param Survey $survey
     * @param int $count
     * @param string $prefix
     * @return array
     * @throws \RuntimeException
     */
    private function _getSurveysAnswers(Survey $survey, $count = 28, $prefix = ''): array
    {
        /** @var Simple $questions */
        $questions = $survey->getSurveyQuestions();
        $answers = [];
//        $user = $this->getAuthenticated(); todo remove after testing
        /** @var SurveyQuestion $question */
        foreach ($questions as $question) {
            $answer = Answer::findFirst([
                'conditions' => 'questionId = ?1 AND userId = ?2',
                'bind' => [
                    1 => $question->id,
                    2 => $this->getAuthenticatedId(),
                ],
            ]);
            if ($answer instanceof Answer) {
                $answers[] = $answer;
            }
        }

        if (\count($answers) !== $count) {
            throw new \RuntimeException($prefix . ' Survey not fully evaluated '. \count($answers) . '  ' . $count);
        }

        return $answers;
    }

    /**
     * @param SurveyTemplate $surveyTemplate
     * @return int
     * @throws \RuntimeException
     */
    private function _createSurvey(SurveyTemplate $surveyTemplate): int
    {
        if ($surveyTemplate instanceof SurveyTemplate) {
            $survey = new Survey();
            $survey->title = $surveyTemplate->title;
            $survey->description = $surveyTemplate->description;
            $survey->isEditable = $surveyTemplate->isEditable;
            $survey->isOlset = $surveyTemplate->isOlset;
            $survey->creator = $this->getAuthenticatedId();
            $survey->organization_id = $surveyTemplate->organization_id;
            if (!empty($surveyTemplate->show_extra_info_and_tags)) {
                $survey->show_extra_info_and_tags = $surveyTemplate->show_extra_info_and_tags;
            } else {
                $survey->show_extra_info_and_tags = false;
            }
            if (!empty($surveyTemplate->tag)) {
                $survey->tag = $surveyTemplate->tag;
            }
            $survey->extra_info = $this->extra_info ?: $surveyTemplate->extra_info;
            $this->extra_info = null;
            if ($survey->save()) {
                $this->extra_info = null;
                $survey->refresh();
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
                foreach ($surveyTemplateQuestions as $data) {
                    $question = $this->createSurveyQuestion($data, $survey->id);
                    if (!$question->save()) {
                        throw new \RuntimeException('Questions not saved ');
                    }
                }
                return $survey->id;
            }
            $this->extra_info = null;
            throw new \RuntimeException(
                'Survey not saved template_id=' . $surveyTemplate->id
                . ' messages = ' . serialize($survey->getMessages())
            );
        }
        $this->extra_info = null;
        throw new \RuntimeException('Survey template not found');
    }

    /**
     * @param \DateTime $year
     * @param Simple $yearSurveys
     * @return bool
     */
    private function _checkYear(\DateTime $year, Simple $yearSurveys): bool
    {
        /** @var ProcessYearSurvey $model */
        foreach ($yearSurveys as $model) {
            $date = new \DateTime($model->date);
            if ($date->format('Y') === $year->format('Y')) {
                return true;
            }
        }
        return false;
    }
}
