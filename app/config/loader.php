<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->libraryDir,
        $config->application->modelsDir,
        $config->application->viewsDir,
        $config->application->controllersDir
    )
)->register();
