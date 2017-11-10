<?php

// autoload_namespaces.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Start\\Test' => array($vendorDir . '/payfort/start/tests'),
    'Start\\' => array($baseDir . '/src'),
    'Start' => array($vendorDir . '/payfort/start/src'),
    'Prophecy\\' => array($vendorDir . '/phpspec/prophecy/src'),
    'Postmark\\' => array($baseDir . '/src', $vendorDir . '/wildbit/postmark-php/src'),
    'Mockery' => array($vendorDir . '/mockery/mockery/library'),
    'Jenssegers\\Mongodb' => array($vendorDir . '/jenssegers/mongodb/src'),
);