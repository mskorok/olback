<?php

namespace App\Model;

use App\Constants\Services;
use App\Mvc\DateTrackingModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

/**
 * User
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2018-08-07, 13:50:04
 */
class User extends DateTrackingModel
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
    public $role;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $password;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $firstName;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $lastName;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $location;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $updated_at;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('user');
        $this->hasMany('id', ActionListGroup::class, 'created_by', ['alias' => 'ActionListGroup']);
        $this->hasMany('id', Answer::class, 'userId', ['alias' => 'Answers']);
        $this->hasMany('id', Group::class, 'creatorId', ['alias' => 'Groups']);
        $this->hasMany('id', Organization::class, 'userId', ['alias' => 'Organization']);
        $this->hasMany('id', ProcessUsers::class, 'userId', ['alias' => 'ProcessUsers']);
        $this->hasMany('id', Survey::class, 'creator', ['alias' => 'Survey']);
        $this->hasMany('id', SurveyTemplate::class, 'creator', ['alias' => 'SurveyTemplates']);
        $this->hasMany('id', SystemicMapItems::class, 'userId', ['alias' => 'SystemicMapItems']);
        $this->hasMany('id', SystemicStructureMapItems::class, 'userId', ['alias' => 'SystemicStructureMapItems']);
        $this->hasMany('id', UserDepartment::class, 'user_id', ['alias' => 'UserDepartment']);
        $this->hasMany('id', UserOrganization::class, 'user_id', ['alias' => 'UserOrganization']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]|User|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User|\Phalcon\Mvc\Model\ResultInterface
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
            'role' => 'role',
            'email' => 'email',
            'username' => 'username',
            'password' => 'password',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
            'location' => 'location',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ];
    }
}
