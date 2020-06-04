<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 7-4-2020 22:16
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace BPN\Typo3LoginService\Controller;

use BPN\BpnWhitelist\Controller\RemoteWhitelistController;
use BPN\Typo3LoginService\Controller\Login\CodeLoginController;
use BPN\Typo3LoginService\Controller\Login\EidLoginController;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

class EidRequestController
{
    /**
     * Handles the request
     * use by calling
     * - index.php?eID=tx_typo3loginservice&action=test_eidinit
     *
     * @return mixed
     * @noinspection PhpUnused
     */
    public function handleRequest(ServerRequestInterface $request): JsonResponse
    {
        if (!RemoteWhitelistController::isHostAllowed('typo3_login_service')) {
            header(HttpUtility::HTTP_STATUS_403);
            return $this->json([
                'error' => 'access denied',
                'code'  => 403,
                'icode' => 1586274077
            ]);
        }

        $method = $request->getQueryParams()['action'] ?? '';
        switch ($method) {
            case 'test_eidinit':
                /** @var EidLoginController $loginController */
                $eidLoginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(EidLoginController::class);
                $authenticated = $eidLoginController->initFeUser();
                $result = [
                    'request'       => $request->getQueryParams(),
                    'authenticated' => [
                        'status' => $authenticated,
                        'userid' => $eidLoginController->getAuthenticatedUserId()
                    ]
                ];
                break;

            case 'test_loginuser':
                $userid = GeneralUtility::_GP('userid');
                /** @var CodeLoginController $eidLoginController */
                $loginController = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(CodeLoginController::class);
                $authenticated = $loginController->loginUser($userid);
                $result = [
                    'user'          => $userid,
                    'authenticated' => [
                        'status' => $authenticated,
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
            return $this->json($result);
        }

        return $this->json(['error' => 'no result']);
    }

    /**
     * @param array $result
     * @return JsonResponse
     */
    private function json(array $result): JsonResponse
    {
        /** @var JsonResponse $jsonResponse */
        return GeneralUtility::makeInstance(JsonResponse::class, $result);
    }
}
