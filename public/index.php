<?php
use Phalcon\Di\FactoryDefault;
date_default_timezone_set('PRC');
ini_set('display_errors','1');
error_reporting(E_ALL&~E_NOTICE&~E_WARNING);
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,token");
header('Access-Control-Allow-Methods: OPTIONS,POST,GET,PUT,DELETE');
header("Content-type: text/html; charset=utf-8");

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    // echo str_replace(["\n","\r","\t"], '***', $application->handle()->getContent());
    echo $application->handle()->getContent();

} catch (\Exception $e) {
    $log = Log::start();
    $log->error($e->getMessage());
    echo $e->getMessage() . '<br>';
    // echo '<pre>' . $e->getTraceAsString() . '</pre>';
}