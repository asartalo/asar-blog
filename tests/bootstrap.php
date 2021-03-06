<?php
/**
* This file is part of the Asar Blog
*
* (c) Wayne Duran <asartalo@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

// Set the timezone manually...
date_default_timezone_set('Asia/Manila');

$srcPath = realpath(__DIR__ . '/../src');
$vendorPath = realpath(__DIR__ . '/../vendor');
$testDataPath = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'data';
$testTempPath = $testDataPath . DIRECTORY_SEPARATOR . 'temp';
if (!file_exists($testDataPath)) {
    mkdir($testDataPath);
}
if (!file_exists($testTempPath)) {
    mkdir($testTempPath);
}
define('ASAR_TESTHELPER_TEMPDIRECTORY', $testTempPath);

require_once $vendorPath . '/autoload.php';

