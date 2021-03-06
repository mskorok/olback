<?php

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * ProcessYearSurvey
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-19, 10:39:37
 * @method Survey getSurvey
 * @method Process getProcess
 * @method Survey getReality
 * @method Survey getVision
 */
class ProcessYearSurvey extends Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $process_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $survey_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $reality;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $vision;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $date;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('process_year_survey');
        $this->belongsTo('process_id', Process::class, 'id', ['alias' => 'Process']);
        $this->belongsTo('reality', Survey::class, 'id', ['alias' => 'Reality']);
        $this->belongsTo('survey_id', Survey::class, 'id', ['alias' => 'Survey']);
        $this->belongsTo('vision', Survey::class, 'id', ['alias' => 'Vision']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'process_year_survey';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProcessYearSurvey[]|ProcessYearSurvey|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProcessYearSurvey|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap(): array
    {
        return [
            'id' => 'id',
            'process_id' => 'process_id',
            'survey_id' => 'survey_id',
            'reality' => 'reality',
            'vision' => 'vision',
            'date' => 'date'
        ];
    }
}
