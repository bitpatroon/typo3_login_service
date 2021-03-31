<?php

if (!defined('TYPO3_MODE')) {
    die('¯\_(ツ)_/¯');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_typo3loginservice'] =
    \BPN\Typo3LoginService\Controller\EidRequestController::class . '::handleRequest';

