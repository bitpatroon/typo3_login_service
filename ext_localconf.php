<?php

if (!defined('TYPO3_MODE')) {
    die ('¯\_(ツ)_/¯');
}

// register the service
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    'typo3_login_service',
    'auth',
    \BPN\Typo3LoginService\LoginService\AttributeLoginService::class,
    [
        'title'       => 'BitpatroonAttributeLoginService',
        'description' => 'Logs in a user by attributes stored in a cookie',
        'subtype'     => 'getUserFE,authUserFE',
        'available'   => true,
        'priority'    => 250,
        'quality'     => 250,
        'os'          => '',
        'exec'        => '',
        'className'   => \BPN\Typo3LoginService\LoginService\AttributeLoginService::class,
    ]
);
