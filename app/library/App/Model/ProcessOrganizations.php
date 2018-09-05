<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;

/**
 * ProcessOrganizations
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-06, 22:41:35
 * @method Organization getOrganization
 * @method Process getProcess
 */
class ProcessOrganizations extends DateTrackingModel
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
    public $organizationId;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('process_organizations');
        $this->belongsTo('organizationId', Organization::class, 'id', ['alias' => 'Organization']);
        $this->belongsTo('processId', Process::class, 'id', ['alias' => 'Process']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'process_organizations';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProcessOrganizations[]|ProcessOrganizations|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProcessOrganizations|\Phalcon\Mvc\Model\ResultInterface
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
                'organizationId' => 'organizationId'
            ];
    }

}
