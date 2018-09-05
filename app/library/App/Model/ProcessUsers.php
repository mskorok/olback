<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;

/**
 * ProcessUsers
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-06, 22:40:29
 * @method Process getProcess
 * @method User getUser
 */
class ProcessUsers extends DateTrackingModel
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
    public $processId;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $userId;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('process_users');
        $this->belongsTo('processId', Process::class, 'id', ['alias' => 'Process']);
        $this->belongsTo('userId', User::class, 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'process_users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProcessUsers[]|ProcessUsers|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProcessUsers|\Phalcon\Mvc\Model\ResultInterface
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
                'processId' => 'processId',
                'userId' => 'userId'
            ];
    }

}
