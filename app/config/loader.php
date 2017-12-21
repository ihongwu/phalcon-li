<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir,
    ]
);


/**
 * 注册函数文件
 */
$loader->registerFiles(
    [
        APP_PATH."/library/function/functions.php",
    ]
);

$loader->register();
