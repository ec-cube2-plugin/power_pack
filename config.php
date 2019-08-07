<?php

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../../vendor/autoload.php';
}

/**
 * @package PowerPack
 */
$objPage = new LC_Page_Admin_PowerPack_Config();
$objPage->init();
$objPage->process();
