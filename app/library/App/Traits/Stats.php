<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 29.09.18
 * Time: 18:17
 */

namespace App\Traits;

use App\Constants\Services;
use App\Model\Answer;
use App\Model\Organization;
use App\Model\Process;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\User;
use App\Model\UserOrganization;
use Phalcon\Mvc\Model\Resultset\Simple;

trait Stats
{
    /**
     * @param Process $process
     * @return int
     * @throws \RuntimeException
     */
    protected function getOlsetIndexCompare(Process $process): int
    {
        $initialSurvey = $process->getSurveyInitial();
        $evaluationSurvey = $process->getSurveyEvaluation();
        $answersCount = $this->getEvaluationSurveyAnswersCount($evaluationSurvey);
        if ($answersCount > 1) {
            $answers = $this->getIndexDiffFromEvaluationSurvey($evaluationSurvey);
        } elseif ($answersCount === 0) {
            $answers = 0;
        } else {
            $answers = $this->getIndexDiffFromSurveys($initialSurvey, $evaluationSurvey);
        }
        return $answers;
    }

    /**
     * @param Process $process
     * @return int
     */
    protected function getParticipatedUsersCompare(Process $process): int
    {
        /** @var Organization $organization */
        $organization = $process->getOrganization();
        /** @var Simple $usersOrganization */
        $usersOrganization = $organization->getUserOrganization();
        $users = [];
        /** @var UserOrganization $model */
        foreach ($usersOrganization as $model) {
            $users[] = $model->getUser();
        }
        /** @var Survey $survey */
        $survey = $process->getSurveyInitial();
        /** @var Simple $surveyQuestions */
        $surveyQuestions = $survey->getSurveyQuestions();
        $answeredUsers = [];
        /** @var SurveyQuestion $surveyQuestion */
        foreach ($surveyQuestions as $surveyQuestion) {
            /** @var Answer $answer */
            /** @var Simple $answers */
            $answers = $surveyQuestion->getAnswers();
            foreach ($answers as $answer) {
                $answeredUser = $answer->getUser();
                if ($answeredUser instanceof User) {
                    $answeredUsers[$answeredUser->id] = $answeredUser;
                }
            }
        }

        return \count($users) > 0 ? \count($answeredUsers)/\count($users) : 0;
    }

    /**
     * @param Survey $survey
     * @return int
     */
    private function getEvaluationSurveyAnswersCount(Survey $survey): int
    {
        /** @var Simple $surveyQuestions */
        $surveyQuestions = $survey->getSurveyQuestions();

        /** @var SurveyQuestion $surveyQuestion */
        $surveyQuestion = $surveyQuestions->getFirst();
        /** @var Simple $questionAnswers */
        $questionAnswers = $surveyQuestion->getAnswers();
        return $questionAnswers->count();
    }

    /**
     * @param Survey $survey
     * @return int
     * @throws \RuntimeException
     */
    private function getIndexDiffFromEvaluationSurvey(Survey $survey): int
    {
        /** @var Simple $surveyQuestions */
        $surveyQuestions = $survey->getSurveyQuestions();

        /** @var SurveyQuestion $surveyQuestion */
        /** @var Simple $questionAnswers */
        $answers = [];
        /** @var SurveyQuestion $surveyQuestion */
        foreach ($surveyQuestions as $surveyQuestion) {
            /** @var Answer $answer */
            /** @var Simple $questionAnswers */
            $questionAnswers = $surveyQuestion->getAnswers();
            $lastAnswer = $questionAnswers->getLast();
            if (!($lastAnswer instanceof Answer)) {
                throw new \RuntimeException('Last Answer not found');
            }
            $count = $questionAnswers->count();
            $index = --$count;
            $previousAnswer = $questionAnswers->offsetGet($index);
            if (!($previousAnswer instanceof Answer)) {
                throw new \RuntimeException('Previous Answer not found');
            }
            $answers = [$previousAnswer, $lastAnswer];
        }
        return $this->calculateOlsetIndex($answers);
    }

    /**
     * @param Survey $initial
     * @param Survey $evaluation
     * @return int
     * @throws \RuntimeException
     */
    private function getIndexDiffFromSurveys(Survey $initial, Survey $evaluation): int
    {
        /** @var Simple $surveyQuestionsInitial */
        $surveyQuestionsInitial = $initial->getSurveyQuestions();
        /** @var Simple $surveyQuestionsEvaluation */
        $surveyQuestionsEvaluation = $evaluation->getSurveyQuestions();
        $answers = [];
        $evaluationAnswers = [];
        /** @var SurveyQuestion $model */
        foreach ($surveyQuestionsEvaluation as $model) {
            /** @var Simple $answersCollection */
            $answersCollection = $model->getAnswers();
            $evaluationAnswers[] = $answersCollection->getFirst();
        }

        $config = $this->getDI()->get(Services::CONFIG);


        $initAnswers = [];
        /** @var SurveyQuestion $model */
        foreach ($surveyQuestionsInitial as $model) {
            /** @var Simple $answersCollection */
            $answersCollection = $model->getAnswers();
            $initAnswers[] = $answersCollection->getFirst();
        }

        if (\count($initAnswers) !== \count($evaluationAnswers)) {
            throw new \RuntimeException('Questions not answered initial='.$initial->id.' evaluation='.$evaluation->id);
        }

        if ($config->application->survey->evaluationCount !== \count($evaluationAnswers)) {
            throw new \RuntimeException('Not answered questions initial='.$initial->id.' evaluation='.$evaluation->id);
        }

        $count = $config->application->survey->evaluationCount;

        for ($i=0; $i < $count; $i++) {
            $answers = [$initAnswers[$i], $evaluationAnswers[$i]];
        }

        return $this->calculateOlsetIndex($answers);
    }

    /**
     * @param array $answers
     * @return int
     */
    private function calculateOlsetIndex(array $answers): int
    {
        return 1;
    }
}
