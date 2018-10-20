<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 08.10.18
 * Time: 6:43
 */

namespace App\Traits;

use App\Constants\Services;
use App\Model\Answer;
use App\Model\GroupReport;
use App\Model\Organization;
use App\Model\Process;
use App\Model\QuestionGroups;
use App\Model\SingleReport;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\User;
use App\Model\UserOrganization;
use App\Services\OlsetIndex;
use mikehaertl\wkhtmlto\Pdf;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\View\Simple as View;

trait Reports
{

    private $initial = false;

    /**
     * @return string
     * @throws \RuntimeException
     */
    protected function getSingleReport(): string
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $report = SingleReport::findFirst([
            'conditions' => 'user_id = ?1',
            'bind' => [
                1 => $user->id
            ],
        ]);
        return $report instanceof SingleReport ? $report->report : '';
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    protected function getGroupReport(): string
    {
        /** @var UserOrganization $userOrganization */
        $userOrganization = $this->getAuthUserOrganization();
        /** @var Organization $organization */
        $organization = $userOrganization->getOrganization();
        $report = GroupReport::findFirst([
            'conditions' => 'organization_id = ?1',
            'bind' => [
                1 => $organization->id
            ],
        ]);
        return $report instanceof GroupReport ? $report->report : '';
    }


    /**
     * @return array
     * @throws \RuntimeException
     */
    protected function getSingleReportData(): array
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $reportStartDate = $this->getSingleReportStartDate();
        $reportEndDate = $this->getSingleReportEndDate();
        $personName = $this->getPersonName($user);
        $organizationName = $this->getOrganizationName();

        $index = $this->getOlsetIndex();
        $answers = $this->getOlsetAnswers($user);
        $scoresGroupsArray = $this->getScoresGroupsArray($answers);
        $scoresOrderArray = $this->getScoresOrderArray($answers);

        return [
            'reportStartDate' => $reportStartDate,
            'reportEndDate' => $reportEndDate,
            'personName' => $personName,
            'role' => $user->role,
            'organizationName' => $organizationName,
            'groups' => $this->getReportGroups(),
            'index' => $this->transformScore($index),
            'scoresGroupsArray' => $this->transformScoresArray($scoresGroupsArray),
            'scoresOrderArray' => $this->transformScoresArray($scoresOrderArray),
            'groupsGraph' => $this->getGraphArray($scoresGroupsArray),
            'orderGraph' => $this->getGraphArray($scoresOrderArray)

        ];
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    protected function getGroupReportData(): array
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $reportStartDate = $this->getGroupReportStartDate();
        $reportEndDate = $this->getGroupReportEndDate();
        $personName = $this->getPersonName($user);
        $organizationName = $this->getOrganizationName();

        $index = $this->getGroupOlsetIndex();
        $answers = $this->getGroupOlsetAnswers();
        $scoresGroupsArray = $this->getGroupScoresArray($answers);
        $scoresOrderArray = $this->getGroupScoresArray($answers, true);

        return [
            'reportStartDate' => $reportStartDate,
            'reportEndDate' => $reportEndDate,
            'personName' => $personName,
            'countByRoles' => $this->getParticipants(),
            'organizationName' => $organizationName,
            'groups' => $this->getReportGroups(),
            'index' => $this->transformScore($index),
            'scoresGroupsArray' => $this->transformScoresArray($scoresGroupsArray),
            'scoresOrderArray' => $this->transformScoresArray($scoresOrderArray),
            'groupsGraph' => $this->getGraphArray($scoresGroupsArray),
            'orderGraph' => $this->getGraphArray($scoresOrderArray)

        ];
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    protected function getParticipants(): array
    {
        $evaluated = $this->getEvaluatedUsers();
        /** @var array $users */
        $users = $evaluated['users'];
        return $this->countByRoles($users);
    }

    /**
     * @param int $limit
     * @return array
     */
    protected function getReportGroups($limit = 7): array
    {
        return QuestionGroups::find([
            'limit' => array('number' => $limit, 'offset' => 0)
        ])->toArray();
    }

    /**
     * @param array $users
     * @return array
     */
    protected function countByRoles(array $users): array
    {
        $res = [];
        foreach ($users as $user) {
            if (!isset($res[$user->role])) {
                $res[$user->role] = 1;
            } else {
                $res[$user->role]++;
            }
        }
        return $res;
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    protected function createSingleReport(): bool
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $report = SingleReport::findFirst([
            'conditions' => 'user_id = ?1',
            'bind' => [
                1 => $user->id
            ],
        ]);
        if (!($report instanceof SingleReport)) {
            $report = new SingleReport();
        }
        $report->user_id = $user->id;
        $report->report = $this->createSingleReportFile();
        if ($report->save()) {
            return true;
        }
        throw new \RuntimeException('Report not saved error ' . serialize($report->getMessages()));
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    protected function createGroupReport(): bool
    {
        /** @var UserOrganization $userOrganization */
        $userOrganization = $this->getAuthUserOrganization();
        /** @var Organization $organization */
        $organization = $userOrganization->getOrganization();
        $report = GroupReport::findFirst([
            'conditions' => 'organization_id = ?1',
            'bind' => [
                1 => $organization->id
            ],
        ]);
        if (!($report instanceof GroupReport)) {
            $report = new GroupReport;
        }
        $report->organization_id = $organization->id;
        $report->report = $this->createGroupReportFile();
        if ($report->save()) {
            return true;
        }
        throw new \RuntimeException('Report not saved error ' . serialize($report->getMessages()));
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function createSingleReportFile(): string
    {
        $array = $this->getSingleReportData();
        return $this->createReportPdf($array);
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function createGroupReportFile(): string
    {
        $array = $this->getGroupReportData();
        return $this->createReportPdf($array, 'group');
    }

    /************************************* CREATE PDF ********************/

    /**
     * @param array $params
     * @param string $prefix
     * @return string
     * @throws \RuntimeException
     */
    private function createReportPdf(array $params, $prefix = 'single'): string
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $conf = $di->get(Services::CONFIG);
        $template = $conf->application->report->dir . $prefix . '.phtml';
        $filename = date('Y-m-d') . '_' . $prefix . '_report_' . md5(uniqid(mt_rand(), false));
        $file = $conf->application->reportUploadDir . $filename . '.pdf';
        $link = $conf->hostName. $conf->application->reportUploadLink . $filename . '.pdf';
        $view = new View();
        $html = $view->render($template, $params);
        $pdf = new Pdf($html);
        if (!$pdf->saveAs($file)) {
            throw new \RuntimeException($pdf->getError());
        }

        return $link;
    }

    /************************************* REPORT DATA ********************/

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getSingleReportStartDate(): string
    {
        return $this->getProcess(true, false)->createdAt;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getGroupReportStartDate(): string
    {
        return $this->getProcess(false, false)->createdAt;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getSingleReportEndDate(): string
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        /** @var Simple $answers */
        $answers = $this->getOlsetAnswers($user);
        $answer = $answers->getFirst();
        if ($answer instanceof Answer) {
            return $answer->createdAt;
        }
        throw new \RuntimeException('Answer not found');
    }


    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getGroupReportEndDate(): string
    {
        /** @var Simple $answers */
        $answers = $this->getLastAnswers(false, true);
        if ($answers->count() === 0) {
            $answers = $this->getLastAnswers(false, false);
        }
        if ($answers->count() !== 0) {
            /** @var Answer $answer */
            $answer = $answers->getFirst();
            if ($answer instanceof Answer) {
                return $answer->createdAt;
            }
        }

        throw new \RuntimeException('Answer not found');
    }

    /**
     * @param User $user
     * @return string
     */
    private function getPersonName(User $user): string
    {
        return $user->firstName . '  ' . $user->lastName;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getOrganizationName(): string
    {
        /** @var UserOrganization $userOrganization */
        $userOrganization = $this->getAuthUserOrganization();
        return $userOrganization->getOrganization()->name;
    }

    /**
     * @param User|ResultInterface|null $user
     * @return float
     * @throws \RuntimeException
     */
    private function getOlsetIndex(User $user = null): float
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        if (null === $user) {
            /** @var User $user */
            $user = $this->getAuthenticated();
        }

        $answers = $this->getOlsetAnswers($user);
        return $service->calculateOlsetIndex($answers);
    }

    /**
     * @return float
     * @throws \RuntimeException
     */
    private function getGroupOlsetIndex(): float
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        $answersArray = $this->getGroupOlsetAnswers();
        $index = 0.00;
        $count = \count($answersArray);

        if ($count === 0) {
            throw new \RuntimeException('Answers not found');
        }

        /** @var Simple $answers */
        foreach ($answersArray as $answers) {
            $index += $service->calculateOlsetIndex($answers);
        }
        return round($index / $count, 2);
    }

    /**
     * @param User $user
     * @return Simple
     * @throws \RuntimeException
     */
    private function getOlsetAnswers(User $user): Simple
    {
        /** @var Organization $org */
        /** @var UserOrganization $userOrganization */
        $userOrganization = $this->getAuthUserOrganization();
        $org = $userOrganization->getOrganization();
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
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
        $query->leftJoin(
            Process::class,
            '[Process].[step3_0] = [Survey].[id]',
            'Process'
        );
        $query->leftJoin(
            Organization::class,
            '[Process].[organizationId] = [Organization].[id]',
            'Organization'
        );
        $query->leftJoin(
            User::class,
            '[User].[id] = [Answer].[userId]',
            'User'
        );
        $query->andWhere('[Organization].[id] = :id:', ['id' => $org->id]);
        $query->andWhere('[User].[id] = :user:', ['user' => $user->id]);
        $query->orderBy('[Answer].[createdAt]');
        $query->limit($config->application->survey->evaluationCount);
        $answers =  $query->getQuery()->execute();
        if ($answers->count === $config->application->survey->evaluationCount) {
            return $answers;
        }
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
        $query->leftJoin(
            Process::class,
            '[Process].[step0] = [Survey].[id]',
            'Process'
        );
        $query->leftJoin(
            Organization::class,
            '[Process].[organizationId] = [Organization].[id]',
            'Organization'
        );
        $query->leftJoin(
            User::class,
            '[User].[id] = [Answer].[userId]',
            'User'
        );
        $query->andWhere('[Organization].[id] = :id:', ['id' => $org->id]);
        $query->andWhere('[User].[id] = :user:', ['user' => $user->id]);
        $query->orderBy('[Answer].[createdAt]');
        $query->limit($config->application->survey->evaluationCount);
        $answers =  $query->getQuery()->execute();
        if ($answers->count === $config->application->survey->evaluationCount) {
            return $answers;
        }
        throw new \RuntimeException('Answers not found');
    }

    /**
     * @param bool $single
     * @param bool $evaluated
     * @return Process
     * @throws \RuntimeException
     */
    protected function getProcess($single = true, $evaluated = true): Process
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        /** @var Simple $answers */
        $answers = $this->getLastAnswers($single, $evaluated);
        if (null === $answers) {
            if ($evaluated) {
                return $this->getProcess($single, false);
            }
            throw new \RuntimeException('Initial survey not evaluated');
        }
        if ($answers->count() === $config->application->survey->initCount) {
            /** @var Answer $answer */
            $answer = $answers->getLast();
            return $this->getProcessFromAnswer($answer);
        }

        throw new \RuntimeException('Incorrect number of answers');
    }

    /**
     * @param Answer $answer
     * @return Process
     * @throws \RuntimeException
     */
    private function getProcessFromAnswer(Answer $answer): Process
    {
        /** @var SurveyQuestion $question */
        $question = $answer->getSurveyQuestions();
        $survey = $question->getSurvey();
        $process = $survey->getProcess30();
        if ($process instanceof Process) {
            return $process;
        }
        $process = $survey->getProcess0();
        if ($process instanceof Process) {
            return $process;
        }
        throw new \RuntimeException('Process not found');
    }

    /**
     * @param bool $single
     * @param bool $evaluated
     * @return ResultsetInterface
     * @throws \RuntimeException
     */
    private function getLastAnswers($single = true, $evaluated = true): ResultsetInterface
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        /** @var User $user */
        $user = $this->getAuthenticated();
        /** @var UserOrganization $userOrganization */
        $userOrganization = $this->getAuthUserOrganization();
        $organization = $userOrganization->getOrganization();
        /** @var Simple $processes */
        $processes = $organization->getProcess();

        $surveys =[];
        /** @var Process $process */
        foreach ($processes as $process) {
            $surveys = $evaluated
                ? $process->getSurveyEvaluation()
                : $process->getSurveyInitial();
        }
        $questions = [];
        $ids = [];
        /** @var Survey $survey */
        foreach ($surveys as $survey) {
            $questions[] = $survey->getSurveyQuestions();
        }
        if (\count($questions) === 0) {
            throw new \RuntimeException('Questions not found');
        }
        /** @var Simple $collection */
        foreach ($questions as $collection) {
            /** @var SurveyQuestion $model */
            foreach ($collection as $model) {
                $ids[] = $model->id;
            }
        }

        $ids = array_unique($ids);

        $builder = Answer::query()
            ->inWhere('questionId', $ids);
        if ($single) {
            $builder->andWhere('userId', $user->id);
        }

        if ($evaluated) {
            $builder->orderBy('createdAt');
        } else {
            $builder->orderBy('createdAt DESC');
        }
        return $builder->limit($config->application->survey->evaluationCount)
            ->execute();
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getGroupOlsetAnswers(): array
    {
        $evaluated = $this->getEvaluatedUsers();
        $answers = [];
        /** @var array $ids */
        $ids = $evaluated['ids'];
        /** @var array $users */
        $users = $evaluated['users'];
        /** @var User $creator */
        foreach ($ids as $id) {
            $user = $users[$id];
            $answers[$creator->id] = $this->getOlsetAnswers($user);
        }
        return $answers;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getEvaluatedUsers(): array
    {
        /** @var UserOrganization $userOrganization */
        $userOrganization = $this->getAuthUserOrganization();
        $organization = $userOrganization->getOrganization();
        /** @var Simple $processes */
        $processes = $organization->getProcess();


        $users = [];
        $ids = [];
        /** @var Process $process */
        foreach ($processes as $process) {

            /** @var Survey $survey */
            $survey = $process->getSurveyEvaluation();
            /** @var Simple $questions */
            $questions = $survey->getSurveyQuestions();

            try {
                $previousUsers = [];
                $previousIds = [];
                /** @var SurveyQuestion $question */
                foreach ($questions as $question) {
                    /** @var Simple $answers */
                    $answers = $question->getAnswers();
                    if ($answers->count() === 0) {
                        throw new \RuntimeException('Answer not found');
                    }
                    /** @var Answer $answer */
                    foreach ($answers as $answer) {
                        $user = $answer->getUser();
                        if (!\in_array($user->id, $ids, true)) {
                            $previousUsers[$user->id] = $user;
                            $previousIds[] = $user->id;
                        }
                    }
                }
                foreach ($previousIds as $id) {
                    $ids[] = $id;
                }
                foreach ($previousUsers as $user) {
                    $users[$user->id] = $user;
                }
            } catch (\RuntimeException $exception) {
                try {
                    $previousUsers = [];
                    $previousIds = [];
                    /** @var Survey $survey */
                    $survey = $process->getSurveyInitial();
                    /** @var Simple $questions */
                    $questions = $survey->getSurveyQuestions();
                    /** @var SurveyQuestion $question */
                    foreach ($questions as $question) {
                        /** @var Simple $answers */
                        $answers = $question->getAnswers();
                        if ($answers->count() === 0) {
                            throw new \RuntimeException('Answer not found');
                        }
                        /** @var Answer $answer */
                        foreach ($answers as $answer) {
                            $user = $answer->getUser();
                            if (!\in_array($user->id, $ids, true)) {
                                $previousUsers[$user->id] = $user;
                                $previousIds[] = $user->id;
                            }
                        }
                    }
                    foreach ($previousIds as $id) {
                        $ids[] = $id;
                    }
                    foreach ($previousUsers as $user) {
                        $users[$user->id] = $user;
                    }
                } catch (\RuntimeException $e) {
                    continue;
                }
            }
        }
        $ids = array_unique($ids);

        return ['users' => $users, 'ids' => $ids];
    }



//    /**
//     * @param Process $process
//     * @return array
//     * @throws \RuntimeException
//     */
//    private function getGroupOlsetAnswers1(Process $process): array
//    {
//        /** @var User $user */
//        $user = $this->getAuthenticated();
//        if (!\in_array($user->role, [AclRoles::MANAGER, AclRoles::ADMINISTRATOR], true)) {
//            throw new \RuntimeException('You are not allowed!!!');
//        }
//        $users = [];
//        /** @var Survey $survey */
//        $survey = $process->getSurveyEvaluation();
//        /** @var Simple $questions */
//        $questions = $survey->getSurveyQuestions();
//        $ids = [];
//        /** @var SurveyQuestion $question */
//        foreach ($questions as $question) {
//            /** @var Simple $answers */
//            $answers = $question->getAnswers();
//            /** @var Answer $answer */
//            foreach ($answers as $answer) {
//                $user = $answer->getUser();
//                if (!\in_array($user->id, $ids, true)) {
//                    $ids[] = $user->id;
//                    $users[] = $user;
//                }
//            }
//        }
//
//
//        $answers = [];
//        /** @var User $creator */
//        foreach ($users as $creator) {
//            $answers[$creator->id] = $this->getOlsetAnswers($user);
//        }
//        return $answers;
//    }

    /**
     * @param Simple $answers
     * @return array
     * @throws \RuntimeException
     */
    private function getScoresGroupsArray(Simple $answers): array
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        $score = [];
        return $service->calculateArrayScore($answers, $score);
    }

    /**
     * @param array $groupAnswers
     * @param bool $order
     * @return array
     * @throws \RuntimeException
     */
    private function getGroupScoresArray(array $groupAnswers, $order = false): array
    {
        $score = [];
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        /** @var Simple $answers */
        foreach ($groupAnswers as $key => $answers) {
            $score[$key] = $service->calculateArrayScore($answers, [], $order);
        }

        $res = [];
        /** @var array $items */
        foreach ($score as $items) {
            foreach ($items as $key => $item) {
                if (isset($res[$key])) {
                    $res[$key]['count']++;
                    $res[$key]['score'] += $item;
                } else {
                    $res[$key]['count'] = 1;
                    $res[$key]['score'] = $item;
                }
            }
        }

        $result = [];
        foreach ($res as $key => $value) {
            if ((int)$value['count'] === 0) {
                throw new \RuntimeException('Count can be zero');
            }
            $result[$key] = round($value['score']/$value['count'], 2);
        }

        return $result;
    }

    /**
     * @param Simple $answers
     * @return array
     * @throws \RuntimeException
     */
    private function getScoresOrderArray(Simple $answers): array
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        $score = [];
        return $service->calculateArrayScore($answers, $score, true);
    }

    /**
     * @param array $answers
     * @return array
     * @throws \RuntimeException
     */
    private function getGraphArray(array $answers): array
    {
        $graph = [];

        foreach ($answers as $group => $score) {
            $graph[$group] = $this->getSingleGraphArray($score);
        }
        return $graph;
    }

    /**
     * @param array $array
     * @return array
     * @throws \RuntimeException
     */
    private function transformScoresArray(array $array): array
    {
        $res = [];
        foreach ($array as $key => $value) {
            $res[$key] = ['score' => round($value, 2), 'color' => $this->getColor($value)];
        }
        return $res;
    }

    /**
     * @param $score
     * @return array
     * @throws \RuntimeException
     */
    private function transformScore($score): array
    {
        return [
            'score' => round($score, 2),
            'graph' => $this->getSingleGraphArray($score),
            'color' => $this->getColor($score)
        ];
    }

    /**
     * @param $score
     * @return array
     * @throws \RuntimeException
     */
    private function getSingleGraphArray($score): array
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        $white = $config->application->report->bg->white;
        $red = $config->application->report->bg->red;
        $blue = $config->application->report->bg->blue;
//        $whiteColor = $config->application->report->bg->white;
        $first = [
            1 => [
                'color' => '',
                'width' => 99,
                'value' => '',
                'bg' => ''
            ],
            2 => [
                'color' => '',
                'width' => 0,
                'value' => '',
                'bg' => ''
            ]
        ];
        $second = [
            1 => [
                'color' => '',
                'width' => 99,
                'value' => '',
                'bg' => ''
            ],
            2 => [
                'color' => '',
                'width' => 0,
                'value' => '',
                'bg' => ''
            ]
        ];
        $third = [
            1 => [
                'color' => '',
                'width' => 99,
                'value' => '',
                'bg' => ''
            ],
            2 => [
                'color' => '',
                'width' => 0,
                'value' => '',
                'bg' => ''
            ]
        ];
        $fourth = [
            1 => [
                'color' => '',
                'width' => 99,
                'value' => '',
                'bg' => ''
            ],
            2 => [
                'color' => '',
                'width' => 0,
                'value' => '',
                'bg' => ''
            ]
        ];
        if ($score >= -2 && $score < -1) {
            $width1 = round(100 * abs($score + 1), 2);
            $width2 = round(100 - 100 * abs($score + 1), 2);

            if ($width1 >= 50) {
                $width1 -= 4.00;
            }
            if ($width2 >= 50) {
                $width2 -= 4.00;
            }
            $first = [
                1 => [
                    'color' => '',
                    'width' => $width2,
                    'value' => round($score, 2),
                    'bg' => ''
                ],
                2 => [
                    'color' => '',
                    'width' => $width1,
                    'value' => '',
                    'bg' => $red
                ]
            ];
            $second = [
                1 => [
                    'color' => '',
                    'width' => 99,
                    'value' => '',
                    'bg' => $red
                ],
                2 => [
                    'color' => '',
                    'width' => 0,
                    'value' => '',
                    'bg' => $red
                ]
            ];
        } elseif ($score >= -1 && $score < 0) {
            $width1 = round(100 * abs($score), 2);
            $width2 = round(100 - 100 * abs($score), 2);

            if ($width1 >= 50) {
                $width1 -= 4.00;
            }
            if ($width2 >= 50) {
                $width2 -= 4.00;
            }
            $second = [
                1 => [
                    'color' => '',
                    'width' => $width2,
                    'value' => round($score, 2),
                    'bg' => ''
                ],
                2 => [
                    'color' => '',
                    'width' => $width1,
                    'value' => '',
                    'bg' => $red
                ]
            ];
        } elseif ((float)$score === 0.00) {
            $third = [
                1 => [
                    'color' => '',
                    'width' => 99,
                    'value' => $score,
                    'bg' => ''
                ],
                2 => [
                    'color' => '',
                    'width' => 0,
                    'value' => '',
                    'bg' => ''
                ]
            ];
        } elseif ($score > 0 && $score < 1) {
            $width1 = round(100 * abs($score), 2);
            $width2 = round(100 - 100 * abs($score), 2);

            if ($width1 >= 50) {
                $width1 -= 4.00;
            }
            if ($width2 >= 50) {
                $width2 -= 4.00;
            }
            $third = [
                1 => [
                    'color' => '',
                    'width' => $width1,
                    'value' => '',
                    'bg' => $white
                ],
                2 => [
                    'color' => '',
                    'width' => $width2,
                    'value' => round($score, 2),
                    'bg' => ''
                ]
            ];
        } elseif ($score >= 1 && $score <= 2) {
            $third = [
                1 => [
                    'color' => '',
                    'width' => 99,
                    'value' => '',
                    'bg' => $blue
                ],
                2 => [
                    'color' => '',
                    'width' => 0,
                    'value' => '',
                    'bg' => $blue
                ]
            ];
            $width1 = round(100 * abs($score - 1), 2);
            $width2 = round(100 - 100 * abs($score - 1), 2);

            if ($width1 >= 50) {
                $width1 -= 4.00;
            }
            if ($width2 >= 50) {
                $width2 -= 4.00;
            }
            $fourth = [
                1 => [
                    'color' => '',
                    'width' => $width1,
                    'value' => '',
                    'bg' => $blue
                ],
                2 => [
                    'color' => '',
                    'width' => $width2,
                    'value' => round($score, 2),
                    'bg' => ''
                ]
            ];
        } else {
            throw new \RuntimeException('Score is incorrect');
        }

        return [
            1 => $first,
            2 => $second,
            3 => $third,
            4 => $fourth
        ];
    }

    /**
     * @param $score
     * @return array
     * @throws \RuntimeException
     */
    private function getColor($score): array
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $config = $di->get(Services::CONFIG);
        $white = $config->application->report->bg->white;
        $red = $config->application->report->bg->red;
        $blue = $config->application->report->bg->blue;
        $whiteColor = $config->application->report->bg->white;
        if ($score >= -2 && $score < 0) {
            return ['bg' => $red, 'char' => $whiteColor];
        }

        if ($score >= 0 && $score < 1) {
            return ['bg' => $white, 'char' => ''];
        }
        if ($score >= 1 && $score <= 2) {
            return ['bg' => $blue, 'char' => ''];
        }
        throw new \RuntimeException('Score is incorrect ' . $score);
    }

    /**
     * @return bool
     */
    public function isInitial(): bool
    {
        return $this->initial;
    }

    /**
     * @param bool $initial
     */
    public function setInitial(bool $initial): void
    {
        $this->initial = $initial;
    }
}
