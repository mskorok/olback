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
     * @return float
     * @throws \RuntimeException
     */
    protected function getOlsetIndexesCompare(Process $process): float
    {
        $initialSurvey = $process->getSurveyInitial();
        $evaluationSurvey = $process->getSurveyEvaluation();
        $answersCount = $this->getEvaluationSurveyAnswersCount($evaluationSurvey);
        if ($answersCount > 1) {
            $diff = $this->getIndexDiffFromEvaluationSurvey($evaluationSurvey);
        } elseif ($answersCount === 0) {
            $diff = 0;
        } else {
            $diff = $this->getIndexDiffFromSurveys($initialSurvey, $evaluationSurvey);
        }
        return $diff;
    }

    /**
     * @param Process $process
     * @return float
     * @throws \RuntimeException
     */
    protected function getAbsoluteOlsetIndexCompare(Process $process): float
    {
        $initialSurvey = $process->getSurveyInitial();
        $evaluationSurvey = $process->getSurveyEvaluation();
        $answersCount = $this->getEvaluationSurveyAnswersCount($evaluationSurvey);
        if ($answersCount > 1) {
            $ratio = $this->getIndexRatioFromEvaluationSurvey($evaluationSurvey);
        } elseif ($answersCount === 0) {
            $ratio = 0;
        } else {
            $ratio = $this->getIndexRatioFromSurveys($initialSurvey, $evaluationSurvey);
        }
        return $ratio;
    }

    /**
     * @param Process $process
     * @return float
     */
    protected function getParticipatedUsersCompare(Process $process): float
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

        return \count($users) > 0 ? round(\count($answeredUsers) / \count($users), 2) : 0;
    }

    /**
     * @param Survey $survey
     * @return float
     */
    private function getEvaluationSurveyAnswersCount(Survey $survey): float
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
     * @return float
     * @throws \RuntimeException
     */
    private function getIndexRatioFromEvaluationSurvey(Survey $survey): float
    {
        $answers = $this->answersFromEvaluationSurvey($survey);
        return $this->calculateOlsetIndexRatio($answers);
    }

    /**
     * @param Survey $initial
     * @param Survey $evaluation
     * @return float
     * @throws \RuntimeException
     */
    private function getIndexRatioFromSurveys(Survey $initial, Survey $evaluation): float
    {
        $answers = $this->answersFromSurveys($initial, $evaluation);

        return $this->calculateOlsetIndexRatio($answers);
    }

    /**
     * @param Survey $survey
     * @return float
     * @throws \RuntimeException
     */
    private function getIndexDiffFromEvaluationSurvey(Survey $survey): float
    {
        $answers = $this->answersFromEvaluationSurvey($survey);
        return $this->calculateOlsetIndexDiff($answers);
    }

    /**
     * @param Survey $initial
     * @param Survey $evaluation
     * @return float
     * @throws \RuntimeException
     */
    private function getIndexDiffFromSurveys(Survey $initial, Survey $evaluation): float
    {
        $answers = $this->answersFromSurveys($initial, $evaluation);
        return $this->calculateOlsetIndexDiff($answers);
    }

    /**
     * @param array $answers
     * @return float
     * @throws \RuntimeException
     */
    private function calculateOlsetIndex(array $answers): float
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $score = 0;
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            switch ((int) $answer->answer) {
                case 1:
                    $score += -2;
                    break;
                case 2:
                    $score += -1;
                    break;
                case 3:
                    $score += 0.4;
                    break;
                case 4:
                    $score++;
                    break;
                case 5:
                    $score += 2;
                    break;
                default:
                    throw new \RuntimeException('Answer score not found');
            }
        }

        return $score/$config->application->survey->evaluationCount;
    }

    /**
     * @param array $answers
     * @return float
     * @throws \RuntimeException
     */
    private function calculateOlsetIndexDiff(array $answers): float
    {
        $firstIndex = $this->calculateOlsetIndex($answers[0]);
        $secondIndex = $this->calculateOlsetIndex($answers[1]);
        return round($secondIndex - $firstIndex, 2);
    }

    /**
     * @param array $answers
     * @return float
     * @throws \RuntimeException
     */
    private function calculateAbsoluteOlsetIndex(array $answers): float
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $score = 0;
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            switch ((int) $answer->answer) {
                case 1:
                    $score += -2;
                    break;
                case 2:
                    $score += -1;
                    break;
                case 3:
                    $score += 0.4;
                    break;
                case 4:
                    $score++;
                    break;
                case 5:
                    $score += 2;
                    break;
                default:
                    throw new \RuntimeException('Answer score not found');
            }
        }


        return $score/$config->application->survey->evaluationCount + 2;
    }

    /**
     * @param array $answers
     * @return float
     * @throws \RuntimeException
     */
    private function calculateOlsetIndexRatio(array $answers): float
    {
        $firstIndex = $this->calculateAbsoluteOlsetIndex($answers[0]);
        $secondIndex = $this->calculateAbsoluteOlsetIndex($answers[1]);
        return round($secondIndex/$firstIndex, 2);
    }

    /**
     * @param Survey $initial
     * @param Survey $evaluation
     * @return array
     * @throws \RuntimeException
     */
    private function answersFromSurveys(Survey $initial, Survey $evaluation): array
    {
        /** @var Simple $surveyQuestionsInitial */
        $surveyQuestionsInitial = $initial->getSurveyQuestions();
        /** @var Simple $surveyQuestionsEvaluation */
        $surveyQuestionsEvaluation = $evaluation->getSurveyQuestions();

        $evaluationAnswers = [];
        /** @var SurveyQuestion $model */
        foreach ($surveyQuestionsEvaluation as $model) {
            /** @var Simple $answersCollection */
            $answersCollection = $model->getAnswers();
            $firstAnswer = $answersCollection->getFirst();
            if (!($firstAnswer instanceof Answer)) {
                throw new \RuntimeException('Evaluation Answer not found question='.$model->id);
            }
            $evaluationAnswers[] = $firstAnswer;
        }

        $config = $this->getDI()->get(Services::CONFIG);


        $initAnswers = [];
        /** @var SurveyQuestion $model */
        foreach ($surveyQuestionsInitial as $model) {
            /** @var Simple $answersCollection */
            $answersCollection = $model->getAnswers();
            $firstAnswer = $answersCollection->getFirst();
            if (!($firstAnswer instanceof Answer)) {
                throw new \RuntimeException('Initial Answer not found question='.$model->id);
            }
            $initAnswers[] = $firstAnswer;
        }

        if (\count($initAnswers) !== \count($evaluationAnswers)) {
            throw new \RuntimeException(
                'Questions not answered initial=' . $initial->id . ' evaluation=' . $evaluation->id
            );
        }

        if ($config->application->survey->evaluationCount !== \count($evaluationAnswers)) {
            throw new \RuntimeException(
                'Not answered questions initial=' . $initial->id . ' evaluation=' . $evaluation->id
            );
        }
        return [$initAnswers, $evaluationAnswers];
    }

    /**
     * @param Survey $survey
     * @return array
     * @throws \RuntimeException
     */
    private function answersFromEvaluationSurvey(Survey $survey): array
    {
        /** @var Simple $surveyQuestions */
        $surveyQuestions = $survey->getSurveyQuestions();

        /** @var SurveyQuestion $surveyQuestion */
        /** @var Simple $questionAnswers */
        $previousAnswers = [];
        $lastAnswers = [];
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
            $index = $count -2;
            $previousAnswer = $questionAnswers->offsetGet($index);
            if (!($previousAnswer instanceof Answer)) {
                throw new \RuntimeException('Previous Answer not found');
            }
            $previousAnswers[] = $previousAnswer;
            $lastAnswers[] = $lastAnswer;
        }
        return [$previousAnswers, $lastAnswers];
    }
}
