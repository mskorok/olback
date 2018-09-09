<?php

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model;

/**
 * SystemicMapItems
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-03, 16:05:06
 * @method Collection getSystemicMapChainFrom
 * @method Collection getSystemicMapChainTo
 * @method Group getGroups
 * @method SystemicMap getSystemicMap
 * @method User getUser
 */
class SystemicMapItems extends Model
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
    public $systemic_map_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $question;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $proposal;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $groupId;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $userId;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('systemic_map_items');
        $this->hasMany('id', SystemicMapChain::class, 'from_item', ['alias' => 'SystemicMapChainFrom']);
        $this->hasMany('id', SystemicMapChain::class, 'to_item', ['alias' => 'SystemicMapChainTo']);
        $this->belongsTo('groupId', Group::class, 'id', ['alias' => 'Groups']);
        $this->belongsTo('systemic_map_id', SystemicMap::class, 'id', ['alias' => 'SystemicMap']);
        $this->belongsTo('userId', User::class, 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'systemic_map_items';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SystemicMapItems[]|SystemicMapItems|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SystemicMapItems|\Phalcon\Mvc\Model\ResultInterface
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
            'systemic_map_id' => 'systemic_map_id',
            'question' => 'question',
            'proposal' => 'proposal',
            'groupId' => 'groupId',
            'userId' => 'userId'
        ];
    }
}
