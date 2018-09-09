<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 06.09.18
 * Time: 17:55
 */

namespace App\Traits;

use App\Model\Process;
use App\Model\ProcessYearSurvey;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\SurveyTemplate;
use App\Model\SurveyTemplateQuestion;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;

trait Surveys
{
    protected $extraInfo = 'Default';

    protected $evaluationSurvey = '0#3_0';

    protected $aarSurvey = '3_1';

    protected $currentSituationSurvey = '_CS_';

    protected $visionSurvey = '_VS_';


    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function createEvaluationSurvey(): int
    {
        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $this->evaluationSurvey . '%"',
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
        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $this->aarSurvey . '%"',
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
        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $this->currentSituationSurvey . '%"',
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
        $surveyTemplate = SurveyTemplate::findFirst(
            [
                'conditions' => 'tag LIKE "%'. $this->visionSurvey . '%"',
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
        $query->andWhere('[Process].[id] = :id:', ['id' => $id]);
        $query->orderBy('[ProcessYearSurvey].[date]');
        $query->columns([
            '[Process].*',
            '[ProcessYearSurvey].*',
            '[Survey].*',
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

            $this->extraInfo = 'Year Survey';
            $yearSurvey->process_id = $process->id;
            $yearSurvey->survey_id = $this->createEvaluationSurvey();
            $yearSurvey->reality = $this->createCurrentSituationSurvey();
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
            if (isset($surveyTemplate->showExtraInfoAndTags) && !empty($surveyTemplate->showExtraInfoAndTags)) {
                $survey->showExtraInfoAndTags = $surveyTemplate->showExtraInfoAndTags;
            } else {
                $survey->showExtraInfoAndTags = false;
            }
            if (isset($surveyTemplate->tag) && !empty($surveyTemplate->tag)) {
                $survey->tag = $surveyTemplate->tag;
            }
            if (isset($surveyTemplate->extraInfo) && !empty($surveyTemplate->extraInfo)) {
                $survey->extraInfo = $surveyTemplate->extraInfo;
            } else {
                $survey->extraInfo = $this->extraInfo;
            }
            if ($survey->save()) {
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
            throw new \RuntimeException('Survey not saved template_id=' . $surveyTemplate->id);
        }
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
