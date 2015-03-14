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
        $this->hasMany('id', 'User', 'id_department', NULL);
    }

}
