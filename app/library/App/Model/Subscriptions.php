<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;
use League\Fractal\Resource\Collection;

/**
 * Subscriptions
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-10-16, 19:17:16
 * @method Organization getOrganization
 * @method Collection getSubscribers
 * @method User getUser
 * @method Collection getUsers
 */
class Subscriptions extends DateTrackingModel
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
    public $type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $subscriber;

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
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $updated_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $expired_at;

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('subscriptions');
        $this->belongsTo('organization_id', Organization::class, 'id', ['alias' => 'Organization']);
        $this->hasMany('id', Subscribers::class, 'subscription_id', ['alias' => 'Subscribers']);
        $this->belongsTo('subscriber', User::class, 'id', ['alias' => 'User']);
        $this->hasManyToMany(
            'id',
            Subscribers::class,
            'subscription_id',
            'user_id',
            User::class,
            'id',
            [
                'alias' => 'Users',
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'subscriptions';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Subscriptions[]|Subscriptions|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Subscriptions|\Phalcon\Mvc\Model\ResultInterface
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
        return parent::columnMap() + [
                'id' => 'id',
                'type' => 'type',
                'subscriber' => 'subscriber',
                'organization_id' => 'organization_id',
                'description' => 'description',
                'expired_at' => 'expired_at'
            ];
    }
}
