<?php 

use Phalcon\Mvc\Router;

return function(){
	
	$router = new Router();

    //default route
    /*$router->add('/:controller/:action/:params', array(
        'controller' => 1, 'action' => 2, 'params' => 3
    ));
    $router->add('/:controller/:action', array(
        'controller' => 1, 'action' => 2
    ));
    $router->add('/:controller', array(
        'controller' => 1
    ));*/

//GET VERB - GET ELEMENT
    //Get elemets of relationship. Ex: /department/2/user
    $router->addGet('/:controller/:int/([a-zA-Z0-9_-]+)', array(
        'controller'    => 1,
        'action'        => "list",
        'id'            => 2,
        'relationship'  => 3
    ));
    //Get one element. Ex: /user/2
    $router->addGet('/:controller/:int', array(
        'controller' => 1,
        'action'     => "list",
        'id'         => 2
    ));
    //Get all elements. Ex: /user
    $router->addGet('/:controller', array(
        'controller' => 1,
        'action'     => "list"
    ));

//POST VERB - CREATE ELEMENT
    //Create a new element. Ex: /user
    $router->addPost('/:controller', array(
        'controller' => 1,
        'action'     => "save"
    ));

//PUT VERB - UPDATE ELEMENT
    //Update a new element. Ex: /user
    $router->addPut('/:controller/:int', array(
        'controller' => 1,
        'action'     => "save",
        'id'         => 2
    ));


//DELETE VERB - UPDATE ELEMENT
    //Update a new element. Ex: /user
    $router->addDelete('/:controller/:int', array(
        'controller' => 1,
        'action'     => "delete",
        'id'         => 2
    ));


    return $router;
}

?>