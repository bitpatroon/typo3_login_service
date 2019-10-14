<?php

/** @var \BPN\Typo3LoginService\Controller\Start $start */
$start = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\Start::class);
$result = $start->handleRequest();
if (!empty($result)) {
    echo $result;
}
