<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;

/**
 * SurveyTemplatesQuestions
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-13, 11:49:46
 */
class SurveyTemplateQuestion extends DateTrackingModel
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
     * @Column(type="string", nullable=false)
     */
    public $question;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $description;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $answered_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $question_order;

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
    public $question_group_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('survey_templates_questions');
        $this->belongsTo(
            'question_group_id',
            QuestionGroups::class,
            'id',
            ['alias' => 'QuestionGroup']
        );
        $this->belongsTo(
            'survey_id',
            Survey::class,
            'id',
            ['alias' => 'Survey']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'survey_templates_questions';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SurveyTemplateQuestion[]|SurveyTemplateQuestion|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SurveyTemplateQuestion|\Phalcon\Mvc\Model\ResultInterface
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
                'question' => 'question',
                'description' => 'description',
                'answered_type' => 'answered_type',
                'question_order' => 'question_order',
                'survey_id' => 'survey_id',
                'question_group_id' => 'question_group_id',
            ];
    }
}
