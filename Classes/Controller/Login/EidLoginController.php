<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 8-4-2020 21:37
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

use BPN\Typo3LoginService\RequestHandler\v8\EidLoginRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EidLoginController extends AbstractLoginController
{
    /**
     * Initialises the FE user, if the user is not already initialised.
     * Notice this routine does NOT login another user, but only logs in the currently logged in user.
     * @return bool|null
     * @see /eID/tx_typo3loginservice/a/loginuser/
     */
    public function initFeUser()
    {
        $globalRequest = ServerRequestFactory::fromGlobals();

        /** @var EidLoginRequestHandler $eidLoginRequestHandler */
        $eidLoginRequestHandler = GeneralUtility::makeInstance(EidLoginRequestHandler::class);

        if (!$eidLoginRequestHandler->canHandleRequest($globalRequest)) {
            return null;
        }

        $eidLoginRequestHandler->handleRequest();
        return $this->isCurrentFrontendUserLoggedIn();
    }
}
