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
use Phalcon\Mvc\Model\Resultset\Simple;

class OlsetIndex extends Injectable
{
    private $config;

    public function __construct($di, $config)
    {
        $this->setDI($di);
        $this->config = $config;
    }

    /**
     * @param iterable $answers
     * @return float
     * @throws \RuntimeException
     */
    public function calculateOlsetIndex(iterable $answers): float
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
     * @param Simple $answers
     * @param array $score
     * @param bool $order
     * @return array
     * @throws \RuntimeException
     */
    public function calculateArrayScore(Simple $answers, array $score, $order = false): array
    {
        $i = 0;
        $params = [$score, []];
        $res = [];
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $i++;

            $params = $order
                ? $this->getScoreByOrder($answer, $params, $i)
                : $this->getScoreByGroup($answer, $params);
        }
        /** @var $score array */
        [$score, $count] = $params;
        foreach ($score as $key => $value) {
            if ($count[$key]  === 0) {
                throw new \RuntimeException('Count can`t be zero');
            }
            $res[$key] = round($score[$key]/$count[$key], 2);
        }

        return $res;
    }

    /**
     * @param Answer $answer
     * @param array $params
     * @param int $i
     * @return array
     * @throws \RuntimeException
     */
    private function getScoreByOrder(Answer $answer, array $params, int $i): array
    {
        [$score, $count] = $params;
        if (isset($score[$i])) {
            $score[$i] += $this->getAnswerScore($answer);
            $count[$i]++;
        } else {
            $score[$i] = $this->getAnswerScore($answer);
            $count[$i] = 1;
        }

        return [$score, $count];
    }

    /**
     * @param Answer $answer
     * @param array $params
     * @return array
     * @throws \RuntimeException
     */
    private function getScoreByGroup(Answer $answer, array $params): array
    {
        /** @var SurveyQuestion $question */
        $question = $answer->getSurveyQuestions();
        /** @var QuestionGroups $group */
        $group = $question->getQuestionGroup();
        [$score, $count] = $params;
        if (isset($score[$group->name])) {
            $score[$group->name] += $this->getAnswerScore($answer);
            $count[$group->name]++;
        } else {
            $score[$group->name] = $this->getAnswerScore($answer);
            $count[$group->name] = 1;
        }
        return [$score, $count];
    }
}
