<?php

class Department extends \Phalcon\Mvc\Model
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
    public function initialize()
    {
        $this->hasMany('id', 'User', 'id_department', array(
            'foreignKey' => array(
                'message' => 'Department cannot be deleted bacause it\'s used on User'
            )
        ));
    }

}
