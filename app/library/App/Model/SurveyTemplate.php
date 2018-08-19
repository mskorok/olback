<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;

/**
 * SurveyTemplate
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-03, 06:54:53
 */
class SurveyTemplate extends DateTrackingModel
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
     * @Column(type="integer", length=11, nullable=false)
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
    public $showExtraInfoAndTags;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $extraInfo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('survey_templates');
        $this->belongsTo(
            'organization_id',
            Organization::class,
            'id',
            ['alias' => 'Organization']
        );
        $this->belongsTo('creator', User::class, 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'survey_templates';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SurveyTemplate[]|SurveyTemplate|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SurveyTemplate|\Phalcon\Mvc\Model\ResultInterface
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
    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'title' => 'title',
                'description' => 'description',
                'isEditable' => 'isEditable',
                'isOlset' => 'isOlset',
                'creator' => 'creator',
                'organization_id' => 'organization_id',
                'tag' => 'tag',
                'show_extra_info_and_tags' => 'showExtraInfoAndTags',
                'extra_info' => 'extraInfo'
            ];
    }
}
