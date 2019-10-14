<?php

if (!defined('TYPO3_MODE')) {
    die ('¯\_(ツ)_/¯');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_typo3loginservice'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'eID.php';

if (TYPO3_branch === '8.7'){
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository::class] = [
        'className' => \BPN\Typo3LoginService\Domain\Repository\V87\FrontEndUserRepository::class
    ];
}

