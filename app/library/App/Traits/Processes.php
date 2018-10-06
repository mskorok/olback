<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 19.09.18
 * Time: 18:19
 */

namespace App\Traits;

use App\Model\Answer;
use App\Model\Process;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\User;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\ModelInterface;

trait Processes
{
    /**
     * @param User $user
     * @return ModelInterface | null
     */
    public function getFirstProcessByUser(User $user)
    {
        $query = new Builder();
        $query->addFrom(Process::class, 'Process');
        $query->leftJoin(
            Survey::class,
            '[Process].[step0] = [Survey].[id]',
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
        $query->orderBy('[Process].[id]');
        $query->limit(1);
        /** @var Simple $result */
        $result = $query->getQuery()->execute();
        if ($result->count() > 0) {
            return $result->getFirst();
        }
        return null;
    }
}
