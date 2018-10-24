<?php

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * SystemicStructureMapItems
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-07, 13:12:00
 * @method SystemicStructureMapChain getSystemicStructureMapChainFrom
 * @method SystemicStructureMapChain getSystemicStructureMapChainTo
 * @method Group getGroups
 * @method SystemicStructureMap getSystemicStructureMap
 * @method User getUser
 */
class SystemicStructureMapItemsV2 extends Model
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
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $itemType;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('systemic_map_structure_items');
        $this->hasMany(
            'id',
            SystemicStructureMapChain::class,
            'from_item',
            ['alias' => 'SystemicStructureMapChainFrom']
        );
        $this->hasMany(
            'id',
            SystemicStructureMapChain::class,
            'to_item',
            ['alias' => 'SystemicStructureMapChainTo']
        );
        $this->belongsTo(
            'groupId',
            Group::class,
            'id',
            ['alias' => 'Groups']
        );
        $this->belongsTo(
            'systemic_map_id',
            SystemicStructureMap::class,
            'id',
            ['alias' => 'SystemicStructureMap']
        );
        $this->belongsTo('userId', User::class, 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'systemic_map_structure_items';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SystemicStructureMapItems[]|SystemicStructureMapItems|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SystemicStructureMapItems|\Phalcon\Mvc\Model\ResultInterface
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
            'userId' => 'userId',
            'itemType' => 'itemType'
        ];
    }
}
