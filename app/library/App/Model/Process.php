<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;
use League\Fractal\Resource\Collection;
use App\Model\Survey;

/**
 * Process
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-19, 11:03:05
 * @method Collection getProcessDepartments
 * @method Collection getProcessOrganizations
 * @method Collection getProcessUsers
 * @method Collection getProcessYearSurvey
 * @method Collection getSystemicMap
 * @method Collection getSystemicStructureMap
 * @method Collection getOrganization
 * @method Survey getSurvey0
 * @method Survey getSurvey30
 * @method Survey getSurvey31
 * @method Survey getReality
 * @method Survey getVision
 */
class Process extends DateTrackingModel
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
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $title;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $currentReality;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $initialIntentions;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $step0;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $step3_0;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $step3_1;

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
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $status;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $organizationId;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $updated_at;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('process');
        $this->hasMany('id', ProcessDepartments::class, 'processId', ['alias' => 'ProcessDepartments']);
        $this->hasMany('id', ProcessOrganizations::class, 'processId', ['alias' => 'ProcessOrganizations']);
        $this->hasMany('id', ProcessUsers::class, 'processId', ['alias' => 'ProcessUsers']);
        $this->hasMany('id', ProcessYearSurvey::class, 'process_id', ['alias' => 'ProcessYearSurvey']);
        $this->hasMany('id', SystemicMap::class, 'processId', ['alias' => 'SystemicMap']);
        $this->hasMany('id', SystemicStructureMap::class, 'processId', ['alias' => 'SystemicStructureMap']);
        $this->belongsTo('organizationId', Organization::class, 'id', ['alias' => 'Organization']);
        $this->belongsTo('step0', Survey::class, 'id', ['alias' => 'Survey0']);
        $this->belongsTo('step3_0', Survey::class, 'id', ['alias' => 'Survey30']);
        $this->belongsTo('step3_1', Survey::class, 'id', ['alias' => 'Survey31']);
        $this->belongsTo('reality', Survey::class, 'id', ['alias' => 'Reality']);
        $this->belongsTo('vision', Survey::class, 'id', ['alias' => 'Vision']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'process';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Process[]|Process|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Process|\Phalcon\Mvc\Model\ResultInterface
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
        return parent::columnMap() + [
                'id' => 'id',
                'title' => 'title',
                'CurrentReality' => 'CurrentReality',
                'InitialIntentions' => 'InitialIntentions',
                'step0' => 'step0',
                'step3_0' => 'step3_0',
                'step3_1' => 'step3_1',
                'reality' => 'step3_0',
                'vision' => 'vision',
                'status' => 'reality',
                'organizationId' => 'organizationId',
            ];
    }
}
