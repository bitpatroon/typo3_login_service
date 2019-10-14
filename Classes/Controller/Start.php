<?php

namespace BPN\Typo3LoginService\Controller;

use SPL\SplEck\Helpers\RenderHelper;
use SPL\SplFe\Utility\RemoteWhitelist;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

class Start
{

    /**
     * Handles the request
     * @return mixed
     */
    public function handleRequest()
    {
        if (!RemoteWhitelist::isHostAllowed('typo3_login_service')) {
            header(HttpUtility::HTTP_STATUS_403);
            return RenderHelper::renderError('Access denied');
        }

        $request = GeneralUtility::_GP('request');
        switch ($request) {
            case 'test_eidinit':
                /** @var \BPN\Typo3LoginService\Controller\EidLoginController $loginController */
                $loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\EidLoginController::class);
                $authenticaticated = $loginController->initFeUser();
                $result = [
                    'request' => $request,
                    'authenticated' => [
                        'status' => $authenticaticated,
                        'userid' => $loginController->getAuthenticatedUserId()
                    ]
                ];
                break;

            case 'test_loginuser':
                $userid = GeneralUtility::_GP('userid');
                /** @var \BPN\Typo3LoginService\Controller\CodeLoginController $eidLoginController */
                $loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\BPN\Typo3LoginService\Controller\CodeLoginController::class);
                $authenticaticated = $loginController->loginUser($userid);
                $result = [
                    'user' => $userid,
                    'authenticated' => [
                        'status' => $authenticaticated,
                        'userid' => $loginController->getAuthenticatedUserId()
                    ]
                ];
                break;

            default:
                // redirect to home
                HttpUtility::redirect('/');
                break;
        }

        if (!empty($result)) {
            return json_encode($result);
        }

        return null;
    }
}