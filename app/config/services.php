<?php

use Phalcon\DI\FactoryDefault,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Mvc\Url as UrlResolver,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter,
    Phalcon\Session\Adapter\Files as SessionAdapter,
    Phalcon\Translate\Adapter\NativeArray;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Registering a router
 */
$di->set('router', function(){
    require 'routes.php';
    return $router;
})
;

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
 $di->setShared('db', function () use ($config) {
     $dbConfig = $config->database->toArray();
     $adapter = $dbConfig['adapter'];
     unset($dbConfig['adapter']);

     $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

     return new $class($dbConfig);
 });

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Setting up the view component
 */
$di->set('view', function() use ($config){
    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines(array(
        ".phtml" => "Phalcon\Mvc\View\Engine\Php"
    ));
    return $view;
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
* Registering a dispatcher
*/
$di->set('dispatcher', function(){
    $dispatcher = new Dispatcher();
    return $dispatcher;
});
