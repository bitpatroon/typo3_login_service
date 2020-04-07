<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 2-9-2019 16:39
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

use BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository;
use TYPO3\CMS\Core\Service\AbstractService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class CodeLoginService extends AbstractService implements SingletonInterface
{
    /**
     * @var int
     */
    private $targetUserId;

    /**
     * @return $array
     */
    private $userRecord;

    /**
     * @param int $targetUserId
     */
    public function setTargetUserId(int $targetUserId): void
    {
        $this->targetUserId = $targetUserId;
    }


    /**
     * Initialize authentication service. (only called when not logged in yet)
     *
     * @param string $mode      Subtype of the service which is used to call the service.
     * @param array  $loginData Submitted login form data.
     * @param array  $authInfo  Information array. Holds submitted form data etc.
     * @param object $pObj      Parent object.
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function initAuth(
        /** @noinspection PhpUnusedParameterInspection */
        $mode,
        $loginData,
        $authInfo,
        $pObj
    ) {
        if (empty($this->targetUserId)) {
            return;
        }

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var FrontEndUserRepository $frontEndUserRepository */
        $frontEndUserRepository = $objectManager->get(FrontEndUserRepository::class);
        /** @var QueryResultInterface|array $user */
        $this->userRecord = $frontEndUserRepository->getByUid($this->targetUserId);
        $this->targetUserId = 0;
    }

    /**
     * Find a user. (eg. look up the user record in database when a login is sent)
     * @return    mixed User array or FALSE.
     * @throws \Exception
     */
    public function getUser()
    {
        if (!empty($this->userRecord)) {
            return $this->userRecord;
        }

        unset($this->userRecord);
        return [];
    }

    /**
     * Authenticate a user. (Check various conditions for the user that might invalidate its authentication, eg. password match, domain, IP, etc.)
     *
     * @param array $user Data of user.
     * @return    bool    Possible return values:
     *
     * 200 - authenticated and no more checking needed - useful for IP checking without password.
     * 100 - Just go on. User is not authenticated but there's still no reason to stop.
     * false - this service was the right one to authenticate the user but it failed.
     * true - this service was able to authenticate the user.
     */
    public function authUser($user)
    {
        if (!empty($user)){
            return 200;
        }
        return 0;
    }

}