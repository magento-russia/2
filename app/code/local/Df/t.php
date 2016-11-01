<?php
/** @var string $file */
$file = str_replace('\\', '/', __FILE__);
/** @var string $base */
$base = substr($file, 0, strpos($file, 'app/code/local'));
/** @noinspection PhpIncludeInspection */
require_once "{$base}/app/Mage.php";
Mage::setIsDeveloperMode(true);
//Mage::app('default');
Mage::app('en');
Mage::dispatchEvent('default');
/** http://stackoverflow.com/a/4059399 */
ob_start();