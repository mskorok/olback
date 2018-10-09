<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.10.18
 * Time: 13:53
 */
namespace App\Services;

use App\Model\Answer;
use App\Model\QuestionGroups;
use App\Model\SurveyQuestion;
use Phalcon\DI\Injectable;

class OlsetIndex extends Injectable
{
    private $config;

    public function __construct($di, $config)
    {
        $this->setDI($di);
        $this->config = $config;
    }

    /**
     * @param array $answers
     * @return float
     * @throws \RuntimeException
     */
    public function calculateOlsetIndex(array $answers): float
    {
        $score = 0.00;
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $score += $this->getAnswerScore($answer);
        }

        return $score/$this->config->application->survey->evaluationCount;
    }

    /**
     * @param Answer $answer
     * @return float
     * @throws \RuntimeException
     */
    public function getAnswerScore(Answer $answer): float
    {
        switch ((int) $answer->answer) {
            case 1:
                return -2.00;
            case 2:
                return -1.00;
            case 3:
                return 0.40;
            case 4:
                return 1.00;
            case 5:
                return 2.00;
            default:
                throw new \RuntimeException('Answer score not found');
        }
    }

    /**
     * @param array $answers
     * @param array $score
     * @param bool $order
     * @return array
     * @throws \RuntimeException
     */
    public function calculateArrayScore(array $answers, array $score, $order = false): array
    {
        $i = 0;
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $i++;

            $score = $order ? $this->getScoreByOrder($answer, $score, $i) : $this->getScoreByGroup($answer, $score);
        }

        return $score;
    }

    /**
     * @param Answer $answer
     * @param array $score
     * @param int $i
     * @return array
     * @throws \RuntimeException
     */
    private function getScoreByOrder(Answer $answer, array $score, int $i): array
    {
        if (isset($score[$i])) {
            $score[$i] += $this->getAnswerScore($answer);
        } else {
            $score[$i] = $this->getAnswerScore($answer);
        }
        return $score;
    }

    /**
     * @param Answer $answer
     * @param array $score
     * @return array
     * @throws \RuntimeException
     */
    private function getScoreByGroup(Answer $answer, array $score): array
    {
        /** @var SurveyQuestion $question */
        $question = $answer->getSurveyQuestions();
        /** @var QuestionGroups $group */
        $group = $question->getQuestionGroup();
        if (isset($score[$group->name])) {
            $score[$group->name] += $this->getAnswerScore($answer);
        } else {
            $score[$group->name] = $this->getAnswerScore($answer);
        }
        return $score;
    }
}
