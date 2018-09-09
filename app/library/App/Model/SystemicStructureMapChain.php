<?php

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * SystemicStructureMapChain
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-07, 13:12:45
 * @method SystemicStructureMapItems getSystemicStructureMapItemsFrom
 * @method SystemicStructureMapItems getSystemicStructureMapItemsTo
 */
class SystemicStructureMapChain extends Model
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
     * @Column(type="integer", length=11, nullable=true)
     */
    public $from_item;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $to_item;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('systemic_structure_map_chain');
        $this->belongsTo(
            'from_item',
            SystemicStructureMapItems::class,
            'id',
            ['alias' => 'SystemicStructureMapItemsFrom']
        );
        $this->belongsTo(
            'to_item',
            SystemicStructureMapItems::class,
            'id',
            ['alias' => 'SystemicStructureMapItemsTo']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'systemic_structure_map_chain';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SystemicStructureMapChain[]|SystemicStructureMapChain|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SystemicStructureMapChain|\Phalcon\Mvc\Model\ResultInterface
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
            'from_item' => 'from_item',
            'to_item' => 'to_item'
        ];
    }
}
