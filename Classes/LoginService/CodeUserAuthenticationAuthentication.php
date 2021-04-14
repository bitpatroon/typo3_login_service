<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 8-4-2021 19:02
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

namespace BPN\Typo3LoginService\LoginService;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class CodeUserAuthenticationAuthentication extends FrontendUserAuthentication
{
    public $authenticated = false;

    public function start()
    {
        $this->id = $this->createSessionId();
        $this->newSessionID = true;

        $userId = $this->getUserId();

        $tempUser = [$this->userid_column => $userId];
        $sessionData = $this->createUserSession($tempUser);
        $this->user = array_merge(
            $tempUser,
            $sessionData
        );

        $this->setSessionCookie();

        $this->authenticated = true;
    }

    private function getUserId() : int
    {
        /** @var CodeLoginService $codeLoginService */
        $codeLoginService = GeneralUtility::makeInstance(CodeLoginService::class);
        $codeLoginService->initAuth('FE', [], [], $this);
        $user = $codeLoginService->getUser();

        // check if is a (valid) user
        $userUid = $this->getUser($user['uid']);
        if (!$userUid) {
            throw new \RuntimeException(
                'Cannot login an invalid user',
                1617902339
            );
        }

        return $userUid;
    }

    private function getUser(int $uid) : int
    {
        /** @var FrontendUserRepository $feUserRepo */
        $feUserRepo = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(FrontendUserRepository::class);
        /** @var FrontendUser $feUser */
        $feUser = $feUserRepo->findByUid($uid);
        if ($feUser) {
            return $feUser->getUid();
        }

        return 0;
    }

}
