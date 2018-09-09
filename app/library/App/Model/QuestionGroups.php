<?php

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model;

/**
 * QuestionGroups
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-03, 07:39:01
 * @method Collection getSurveyQuestions
 * @method Collection getSurveyTemplatesQuestions
 */
class QuestionGroups extends Model
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
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $description;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('question_group');
        $this->hasMany(
            'id',
            SurveyQuestion::class,
            'question_group_id',
            ['alias' => 'SurveyQuestions']
        );
        $this->hasMany(
            'id',
            SurveyTemplateQuestion::class,
            'question_group_id',
            ['alias' => 'SurveyTemplatesQuestions']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'question_group';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return QuestionGroups[]|QuestionGroups|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return QuestionGroups|\Phalcon\Mvc\Model\ResultInterface
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
            'name' => 'name',
            'description' => 'description'
        ];
    }
}
