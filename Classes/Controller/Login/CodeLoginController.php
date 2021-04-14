<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 8-4-2020 21:38
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

namespace BPN\Typo3LoginService\Controller\Login;

use BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository;
use BPN\Typo3LoginService\LoginService\CodeLoginService;
use BPN\Typo3LoginService\RequestHandler\CodeLoginRequestHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CodeLoginController extends AbstractLoginController
{
    /**
     * Method logs a user in
     *
     * @param int|string $uidOrUsername
     *
     * @return array|bool|null
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function loginUser($uidOrUsername)
    {
        if (empty($uidOrUsername)) {
            throw new \RuntimeException(
                'Cannot login. No user id or username',
                1567503579121
            );
        }

        $uid = $this->getUid($uidOrUsername);

        /** @var CodeLoginService $codeLoginService */
        $codeLoginService = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(CodeLoginService::class);
        $codeLoginService->setTargetUserId($uid);

        /** @var CodeLoginRequestHandler $requestHandler */
        $requestHandler = GeneralUtility::makeInstance(CodeLoginRequestHandler::class);

        return $requestHandler->handleRequest();
    }

    /**
     * Gets the uid
     *
     * @param string|int $uidOrUsername
     *
     * @return bool|int
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    private function getUid($uidOrUsername)
    {
        $uid = $uidOrUsername;
        if (!is_numeric($uidOrUsername)) {
            /** @var FrontEndUserRepository $frontEndUserRepository */
            $frontEndUserRepository = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(FrontEndUserRepository::class);
            $uid = $frontEndUserRepository->getByUserName($uidOrUsername);
        }

        return $uid;
    }
}
