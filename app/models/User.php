<?php

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class User extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var integer
     */
    public $id_department;
    public function initialize()
    {
        $this->belongsTo('id_department', 'Department', 'id', array('foreignKey' => true));
    }
    
    public function validation()
    {
        $this->validate(
            new PresenceOf(
                array(
                    'field' => 'email',
                    'message' => 'requiredEmail'
                )
            )
        );
        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                    'message'  => 'validEmail'
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
