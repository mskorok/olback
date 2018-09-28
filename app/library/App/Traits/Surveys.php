<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 06.09.18
 * Time: 17:55
 */

namespace App\Traits;

use App\Constants\Services;
use App\Model\Process;
use App\Model\ProcessYearSurvey;
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
    protected function createDemographicsSurvey(User $user): bool//todo
    {
        $config = $this->getDI()->get(Services::CONFIG);
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
                    'conditions' => 'tag LIKE "%'. $config->application->survey->demographics . '%"',
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

        $config = $this->getDI()->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $config->application->survey->evaluation . '%"',
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

        $config = $this->getDI()->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $config->application->survey->init . '%"',
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

        $config = $this->getDI()->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $config->application->survey->aar . '%"',
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

        $config = $this->getDI()->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $config->application->survey->CRS . '%"',
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

        $config = $this->getDI()->get(Services::CONFIG);

        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $config->application->survey->VS . '%"',
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
        $process = Process::findFirst((int) $id);
        $now = new \DateTime();
        $createdDate = $process->createdAt;
        $createdDate = new \DateTime($createdDate);
        $diff = (int) $createdDate->diff($now)->y;
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

    /****************  PRIVATE  **********************/

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
            $survey->creator = $surveyTemplate->creator;
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
