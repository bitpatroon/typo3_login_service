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


class AbstractLoginController
{
    /**
     * Gets the authenticated userid
     * @return bool|int
     */
    public function getAuthenticatedUserId(){
        if (empty($GLOBALS['TSFE'] || empty($GLOBALS['TSFE']->fe_user) || empty($GLOBALS['TSFE']->fe_user->user))){
            return false;
        }

        return (int)$GLOBALS['TSFE']->fe_user->user['uid'];
    }

    /**
     * Tests if the current user is logged in.
     * @param bool $checkStrict
     * @return bool
     */
    public function isCurrentFrontendUserLoggedIn($checkStrict = false)
    {
        if (empty($GLOBALS['TSFE']) || empty($GLOBALS['TSFE']->fe_user) || empty($GLOBALS['TSFE']->fe_user->user)) {
            return false;
        }

        if (!isset($GLOBALS['TSFE']->fe_user->user['uid']) || empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            return false;
        }

        if (!isset($GLOBALS['TSFE']->fe_user->user['username']) || empty($GLOBALS['TSFE']->fe_user->user['username'])) {
            return false;
        }

        if ($checkStrict) {
            return !empty($_COOKIE['fe_typo_user']);
        }

        return true;
    }

}