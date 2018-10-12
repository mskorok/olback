<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 08.10.18
 * Time: 6:43
 */

namespace App\Traits;

use App\Constants\AclRoles;
use App\Constants\Services;
use App\Model\Answer;
use App\Model\GroupReport;
use App\Model\Process;
use App\Model\QuestionGroups;
use App\Model\SingleReport;
use App\Model\Survey;
use App\Model\SurveyQuestion;
use App\Model\User;
use App\Services\OlsetIndex;
use mikehaertl\wkhtmlto\Pdf;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\View\Simple as View;

trait Reports
{

    private $initial = false;

    /**
     * @param Process $process
     * @return string
     * @throws \RuntimeException
     */
    protected function getSingleReport(Process $process): string
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $report = SingleReport::findFirst([
            'conditions' => 'user_id = ?1 AND process_id = ?2',
            'bind' => [
                1 => $user->id,
                2 => $process->id,
            ],
        ]);
        return $report instanceof SingleReport ? $report->report : '';
    }

    /**
     * @param Process $process
     * @return string
     * @throws \RuntimeException
     */
    protected function getGroupReport(Process $process): string
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $organization = $user->getOrganization();
        $report = GroupReport::findFirst([
            'conditions' => 'organization_id = ?1 AND process_id = ?2',
            'bind' => [
                1 => $organization->id,
                2 => $process->id,
            ],
        ]);
        return $report instanceof GroupReport ? $report->report : '';
    }


    /**
     * @param Process $process
     * @return array
     * @throws \RuntimeException
     */
    protected function getSingleReportData(Process $process): array
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $reportStartDate = $this->getReportStartDate($process);
        $reportEndDate = $this->getReportEndDate($process);
        $personName = $this->getPersonName($user);
        $organizationName = $this->getOrganizationName($user);

        $index = $this->getOlsetIndex($process);
        $answers = $this->getOlsetAnswers($process, $user);
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
     * @param Process $process
     * @return array
     * @throws \RuntimeException
     */
    protected function getGroupReportData(Process $process): array
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $reportStartDate = $this->getReportStartDate($process);
        $reportEndDate = $this->getReportEndDate($process);
        $personName = $this->getPersonName($user);
        $organizationName = $this->getOrganizationName($user);

        $index = $this->getGroupOlsetIndex($process);
        $answers = $this->getGroupOlsetAnswers($process);
        $scoresGroupsArray = $this->getGroupScoresArray($answers);
        $scoresOrderArray = $this->getGroupScoresArray($answers, true);

        return [
            'reportStartDate' => $reportStartDate,
            'reportEndDate' => $reportEndDate,
            'personName' => $personName,
            'countByRoles' => $this->getParticipants($process),
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
     * @param Process $process
     * @return array
     */
    protected function getParticipants(Process $process): array
    {
        /** @var Survey $survey */
        $survey = $process->getSurveyEvaluation();
        /** @var Simple $questions */
        $questions = $survey->getSurveyQuestions();
        $ids = [];
        /** @var SurveyQuestion $question */
        foreach ($questions as $question) {
            /** @var Simple $answers */
            $answers = $question->getAnswers();
            /** @var Answer $answer */
            foreach ($answers as $answer) {
                $ids[] = $answer->userId;
            }
        }
        $ids = array_unique($ids);
        $users = [];
        foreach ($ids as $id) {
            $user = User::findFirst((int)$id);
            if ($user instanceof User) {
                $users[] = $user;
            }
        }
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
     * @param Process $process
     * @return bool
     * @throws \RuntimeException
     */
    protected function createSingleReport(Process $process): bool
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $report = SingleReport::findFirst([
            'conditions' => 'user_id = ?1 AND process_id = ?2',
            'bind' => [
                1 => $user->id,
                2 => $process->id,
            ],
        ]);
        if (!($report instanceof SingleReport)) {
            $report = new SingleReport();
        }
        $report->process_id = $process->id;
        $report->user_id = $user->id;
        $report->report = $this->createSingleReportFile($process);
        if ($report->save()) {
            return true;
        }
        throw new \RuntimeException('Report not saved error ' . serialize($report->getMessages()));
    }

    /**
     * @param Process $process
     * @return bool
     * @throws \RuntimeException
     */
    protected function createGroupReport(Process $process): bool
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        $organization = $user->getOrganization();
        $report = GroupReport::findFirst([
            'conditions' => 'organization_id = ?1 AND process_id = ?2',
            'bind' => [
                1 => $organization->id,
                2 => $process->id,
            ],
        ]);
        if (!($report instanceof GroupReport)) {
            $report = new GroupReport;
        }
        $report->process_id = $process->id;
        $report->organization_id = $organization->id;
        $report->report = $this->createGroupReportFile($process);
        if ($report->save()) {
            return true;
        }
        throw new \RuntimeException('Report not saved error ' . serialize($report->getMessages()));
    }

    /**
     * @param Process $process
     * @return string
     * @throws \RuntimeException
     */
    private function createSingleReportFile(Process $process): string
    {
        $array = $this->getSingleReportData($process);
        return $this->createReportPdf($array);
    }

    /**
     * @param Process $process
     * @return string
     * @throws \RuntimeException
     */
    private function createGroupReportFile(Process $process): string
    {
        $array = $this->getGroupReportData($process);
        return $this->createReportPdf($array);
    }

    /************************************* CREATE PDF ********************/

    /**
     * @param array $params
     * @param string $prefix
     * @return string
     * @throws \RuntimeException
     */
    private function createReportPdf(array $params, $prefix = ''): string
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        $conf = $di->get(Services::CONFIG);
        $template = $conf->application->report->dir . 'single.phtml';
        $filename = date('Y-m-d') . '_' . $prefix . '_report_' . md5(uniqid(mt_rand(), false));
        $file = $conf->application->reportUploadDir . $filename . '.pdf';
        $view = new View();
        $html = $view->render($template, $params);
        $pdf = new Pdf($html);
        if (!$pdf->saveAs($file)) {
            throw new \RuntimeException($pdf->getError());
        }

        return $file;
    }

    /************************************* REPORT DATA ********************/

    /**
     * @param Process $process
     * @return string
     */
    private function getReportStartDate(Process $process): string
    {
        return $process->createdAt;
    }

    /**
     * @param Process $process
     * @return string
     * @throws \RuntimeException
     */
    private function getReportEndDate(Process $process): string
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        /** @var Simple $answers */
        $answers = $this->getOlsetAnswers($process, $user);
        $answer = $answers->getFirst();
        if ($answer instanceof Answer) {
            return $answer->createdAt;
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
     * @param User $user
     * @return string
     */
    private function getOrganizationName(User $user): string
    {
        return $user->getOrganization()->name;
    }

    /**
     * @param Process $process
     * @param User|ResultInterface|null $user
     * @return float
     * @throws \RuntimeException
     */
    private function getOlsetIndex(Process $process, User $user = null): float
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        if (null === $user) {
            /** @var User $user */
            $user = $this->getAuthenticated();
        }

        $answers = $this->getOlsetAnswers($process, $user);
        return $service->calculateOlsetIndex($answers);
    }

    /**
     * @param Process $process
     * @return float
     * @throws \RuntimeException
     */
    private function getGroupOlsetIndex(Process $process): float
    {
        /** @var  \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var OlsetIndex $service */
        $service = $di->get(Services::OLSET_INDEX);
        $answersArray = $this->getGroupOlsetAnswers($process);
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
     * @param Process $process
     * @param User $user
     * @return Simple
     */
    private function getOlsetAnswers(Process $process, User $user): Simple
    {
        $survey = $this->initial ? 'step0' : 'step3_0';
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
            '[Process].[' . $survey . '] = [Survey].[id]',
            'Process'
        );
        $query->leftJoin(
            User::class,
            '[User].[id] = [Answer].[userId]',
            'User'
        );
        $query->andWhere('[Process].[id] = :id:', ['id' => $process->id]);
        $query->andWhere('[User].[id] = :user:', ['user' => $user->id]);
        $query->orderBy('[Answer].[createdAt]');
        $query->limit($config->application->survey->evaluationCount);
        return $query->getQuery()->execute();
    }

    /**
     * @param Process $process
     * @return array
     * @throws \RuntimeException
     */
    private function getGroupOlsetAnswers(Process $process): array
    {
        /** @var User $user */
        $user = $this->getAuthenticated();
        if (!\in_array($user->role, [AclRoles::MANAGER, AclRoles::ADMINISTRATOR], true)) {
            throw new \RuntimeException('You are not allowed!!!');
        }
        $users = [];
        /** @var Survey $survey */
        $survey = $process->getSurveyEvaluation();
        /** @var Simple $questions */
        $questions = $survey->getSurveyQuestions();
        $ids = [];
        /** @var SurveyQuestion $question */
        foreach ($questions as $question) {
            /** @var Simple $answers */
            $answers = $question->getAnswers();
            /** @var Answer $answer */
            foreach ($answers as $answer) {
                $user = $answer->getUser();
                if (!\in_array($user->id, $ids, true)) {
                    $ids[] = $user->id;
                    $users[] = $user;
                }
            }
        }


        $answers = [];
        /** @var User $creator */
        foreach ($users as $creator) {
            $answers[$creator->id] = $this->getOlsetAnswers($process, $user);
        }
        return $answers;
    }

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
