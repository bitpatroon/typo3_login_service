<?php

$EM_CONF[$_EXTKEY] = [
    'title'          => 'Login Services',
    'description'    => 'Allows technical logging in',
    'category'       => 'fe',
    'author'         => 'Sjoerd Zonneveld',
    'author_email'   => 'typo3@bitpatroon.nl',
    'state'          => 'stable',
    'author_company' => 'Bitpatroon',
    'version'        => '10.4',
    'constraints'    => [
        'depends' => [
            'typo3' => '10.4.0-10.9.9999',
            'typo3_hooks' => '1.0.0-',
        ],
    ],
];
