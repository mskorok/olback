<?php

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * UserOrganization
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-03, 17:43:02
 * @method Organization getOrganization
 * @method User getUser
 */
class UserOrganization extends Model
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
    public $organization_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $user_id;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('user_organization');
        $this->belongsTo('organization_id', Organization::class, 'id', ['alias' => 'Organization']);
        $this->belongsTo('user_id', User::class, 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'user_organization';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserOrganization[]|UserOrganization|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserOrganization|\Phalcon\Mvc\Model\ResultInterface
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
            'organization_id' => 'organization_id',
            'user_id' => 'user_id'
        ];
    }
}
