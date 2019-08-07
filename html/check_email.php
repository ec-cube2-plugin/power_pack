<?php

require_once './require.php';

$objPage = new LC_Page_CheckEmail();
$objPage->init();
$objPage->process();
