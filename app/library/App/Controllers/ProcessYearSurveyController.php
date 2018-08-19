<?php

namespace App\Controllers;

use App\Model\Process;
use App\Model\ProcessYearSurvey;
use App\Traits\Auth;
use Phalcon\Mvc\Model;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class ProcessYearSurveyController extends CrudResourceController
{
    use Auth;

    /**
     * @param Model $item
     * @throws \RuntimeException
     */
    protected function beforeSave(Model $item): void
    {
        /** @var ProcessYearSurvey $item */
        $process = ProcessYearSurvey::findFirst(
            [
                'conditions' => '	process_id = ?1 ',
                'bind' => [
                    1 => $item->process_id,
                ],
            ]
        );
        if (!($process instanceof Process)) {
            throw new \RuntimeException('Initial ProcessYearSurvey not created');
        }
    }


    /**
     * @param Model $item
     */
    protected function beforeUpdate(Model $item): void
    {
        /** @var ProcessYearSurvey $item */
        $old = ProcessYearSurvey::findFirst($item->id);
        $item->date = $old->date;
        $item->process_id = $old->process_id;
        $item->survey_id = $old->survey_id;
    }
}
