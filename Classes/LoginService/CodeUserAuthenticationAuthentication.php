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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * @deprecated No longer used
 */
class CodeUserAuthenticationAuthentication extends FrontendUserAuthentication
{
    public $authenticated = false;

    public function start()
    {
        $this->authenticated = false;
        $this->id = $this->createSessionId();
        $this->newSessionID = true;

        $tempUser = $this->getAndValidateUser();
        if (!$tempUser) {
            return;
        }

        $sessionData = $this->createUserSession($tempUser);
        $this->user = array_merge(
            $sessionData,
            $tempUser
        );

        $this->setSessionCookie();

        $this->authenticated = true;
    }

    private function getAndValidateUser()
    {
        /** @var CodeLoginService $codeLoginService */
        $codeLoginService = GeneralUtility::makeInstance(CodeLoginService::class);
        $codeLoginService->initAuth('FE', [], [], $this);
        $userRecord = $codeLoginService->getUser();
        if (!$userRecord || !isset($userRecord['uid']) || !(int)$userRecord['uid']) {
            return false;
        }

        // check if is a (valid) user (existing and not hidden and not deleted)
        $uid = (int)$userRecord['uid'];

        /** @var FrontendUserRepository $feUserRepo */
        $feUserRepo = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(FrontendUserRepository::class);
        /** @var FrontendUser $feUser */
        $feUser = $feUserRepo->findByUid($uid);
        if ($feUser && $feUser->getUid()) {
            return $userRecord;
        }

        throw new \RuntimeException(
            'User not found. Cannot continue',
            1617902339
        );
    }

}
