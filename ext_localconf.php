<?php

if (!defined('TYPO3_MODE')) {
    die ('¯\_(ツ)_/¯');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_typo3loginservice'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'eID.php';