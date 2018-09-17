<?php

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model;
use App\Model\SystemicMapItems;

/**
 * Survey
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-03, 10:47:00
 * @method Process getProcess0
 * @method Process getProcess30
 * @method Process getProcess31
 * @method Process getProcessReality
 * @method Process getProcessVision
 * @method Collection getProcessYearSurvey
 * @method Collection getProcessYearRealitySurvey
 * @method Collection getProcessYearVisionSurvey
 * @method Collection getSurveyQuestions
 * @method Collection getSystemicMapItems
 * @method Collection getOrganization
 * @method User getUser
 *
 */
class Survey extends Model
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
     * @Column(type="string", nullable=true)
     */
    public $description;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $isEditable;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $isOlset;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $creator;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $organization_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $tag;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $show_extra_info_and_tags;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $extra_info;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('survey');
        $this->hasOne('id', Process::class, 'reality', ['alias' => 'ProcessReality']);
        $this->hasOne('id', Process::class, 'step0', ['alias' => 'Process0']);
        $this->hasOne('id', Process::class, 'step3_0', ['alias' => 'Process30']);
        $this->hasOne('id', Process::class, 'step3_1', ['alias' => 'Process31']);
        $this->hasOne('id', Process::class, 'vision', ['alias' => 'ProcessVision']);
        $this->hasMany(
            'id',
            ProcessYearSurvey::class,
            'reality',
            ['alias' => 'ProcessYearRealitySurvey']
        );
        $this->hasMany(
            'id',
            ProcessYearSurvey::class,
            'survey_id',
            ['alias' => 'ProcessYearSurvey']
        );
        $this->hasMany(
            'id',
            ProcessYearSurvey::class,
            'vision',
            ['alias' => 'ProcessYearVisionSurvey']
        );
        $this->hasMany(
            'id',
            SurveyQuestion::class,
            'survey_id',
            ['alias' => 'SurveyQuestions']
        );
        $this->hasMany(
            'id',
            SystemicMapItems::class,
            'survey',
            ['alias' => 'SystemicMapItems']
        );
        $this->belongsTo(
            'organization_id',
            Organization::class,
            'id',
            ['alias' => 'Organization']
        );
        $this->belongsTo(
            'creator',
            User::class,
            'id',
            ['alias' => 'User']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'survey';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Survey[]|Survey|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Survey|\Phalcon\Mvc\Model\ResultInterface
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
            'title' => 'title',
            'description' => 'description',
            'isEditable' => 'isEditable',
            'isOlset' => 'isOlset',
            'creator' => 'creator',
            'organization_id' => 'organization_id',
            'show_extra_info_and_tags' => 'show_extra_info_and_tags',
            'extra_info' => 'extra_info',
            'tag' => 'tag'
        ];
    }
}
