<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 22.09.18
 * Time: 14:36
 */

namespace App\Traits;

use App\Constants\Services;
use App\Model\Answer;
use App\Model\Pis;
use App\Model\Process;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\User;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;

trait CheckSteps
{
    /**
     * @param Process $process
     * @param User $user
     * @return array
     */
    protected function getCurrentStepPositions(Process $process, User $user): array
    {
        $pis = Pis::findFirst([
            'conditions' => 'user_id = ?1 AND process_id = ?2',
            'bind' => [
                1 => $user->id,
                2 => $process->id,
            ],
        ]);

        $demographicsSurvey = $this->getDemographicsSurvey($user);

        $hasDemographics = $demographicsSurvey instanceof Survey
            ? $this->getDemographicsAnswers($demographicsSurvey)
            : false;

        $hasInitial = false;
        $hasCRS = false;
        $hasVS = false;
        $hasEvaluation = false;
        $hasAAR = false;

        $hasPis = $pis instanceof Pis;
        if ($hasPis) {
            $hasInitial = $this->checkInitial($process, $user);
            if ($hasInitial) {
                $hasCRS = $this->checkCRS($process, $user);
                if ($hasCRS) {
                    $hasVS = $this->checkVS($process, $user);
                    if ($hasVS) {
                        $hasEvaluation = $this->checkEvaluation($process, $user);
                        if ($hasEvaluation) {
                            $hasAAR = $this->checkAAR($process, $user);
                        }
                    }
                }
            }
        }

        return [
            'hasDemographics' => $hasDemographics,
            'hasPIS' => $hasPis,
            'hasInitial' => $hasInitial,
            'hasCRS' => $hasCRS,
            'hasVS' => $hasVS,
            'hasEvaluation' => $hasEvaluation,
            'hasAAR' => $hasAAR
        ];
    }

    /**
     * @param User $user
     * @return bool
     * @throws \RuntimeException
     */
    protected function checkDemographics(User $user): bool
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $surveys = Survey::findFirst([
            'conditions' => 'creator = ?1 AND tag = ?2',
            'bind' => [
                1 => $user->id,
                2 => $config->survey->demographics,
            ],
        ]);
        if ($surveys instanceof Survey) {
            return true;
        }
        if ($this->createDemographicsSurvey()) {
            return false;
        }
        throw new \RuntimeException('Demographics Survey not created');
    }

    /**
     * @param Process $process
     * @param User $user
     * @return bool
     */
    protected function checkInitial(Process $process, User $user): bool
    {

        return $this->_checkQuery($process, $user, 'step0');
    }


    /**
     * @param Process $process
     * @param User $user
     * @return bool
     */
    protected function checkCRS(Process $process, User $user): bool
    {
        return $this->_checkQuery($process, $user, 'reality');
    }

    /**
     * @param Process $process
     * @param User $user
     * @return bool
     */
    protected function checkVS(Process $process, User $user): bool
    {
        return $this->_checkQuery($process, $user, 'vision');
    }

    /**
     * @param Process $process
     * @param User $user
     * @return bool
     */
    protected function checkEvaluation(Process $process, User $user): bool
    {
        return $this->_checkQuery($process, $user, 'step3_0');
    }

    /**
     * @param Process $process
     * @param User $user
     * @return bool
     */
    protected function checkAAR(Process $process, User $user): bool
    {
        return $this->_checkQuery($process, $user, 'step3_1');
    }

    /**
     * @param Survey $survey
     * @return bool
     */
    protected function getDemographicsAnswers(Survey $survey): bool
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $query = new Builder();
        $query->addFrom(Answer::class, 'Answer');
        $query->leftJoin(
            SurveyQuestion::class,
            '[Question].[id] = [Answer].[questionId]',
            'Question'
        );
        $query->leftJoin(
            Survey::class,
            '[Question].[survey_id] = [Survey].[id]',
            'Survey'
        );

        $query->andWhere('[Survey].[id] = :id:', ['id' => $survey->id]);

        /** @var Simple $result */
        $result = $query->getQuery()->execute();
        return $result->count() === (int)$config->survey->demographics;
    }

    /**
     * @param User $user
     * @return Survey|\Phalcon\Mvc\Model\ResultInterface
     */
    protected function getDemographicsSurvey(User $user)
    {
        $config = $this->getDI()->get(Services::CONFIG);

        return Survey::findFirst([
            'conditions' => 'creator = ?1 AND tag = ?2',
            'bind' => [
                1 => $user->id,
                2 => $config->survey->demographics,
            ],
        ]);
    }

    /**
     * @param Process $process
     * @param User $user
     * @param $step
     * @return bool
     */
    private function _checkQuery(Process $process, User $user, $step): bool
    {
        $query = new Builder();
        $query->addFrom(Process::class, 'Process');
        $query->leftJoin(
            Survey::class,
            '[Process].[' . $step . '] = [Survey].[id]',
            'Survey'
        );
        $query->leftJoin(
            SurveyQuestion::class,
            '[Question].[survey_id] = [Survey].[id]',
            'Question'
        );
        $query->leftJoin(
            Answer::class,
            '[Question].[id] = [Answer].[questionId]',
            'Answer'
        );
        $query->leftJoin(
            User::class,
            '[User].[id] = [Answer].[userId]',
            'User'
        );
        $query->andWhere('[User].[id] = :id:', ['id' => $user->id]);
        $query->andWhere('[Process].[id] = :pid:', ['pid' => $process->id]);
        /** @var Simple $result */
        $result = $query->getQuery()->execute();
        return $result->count() !== 0;
    }
}
