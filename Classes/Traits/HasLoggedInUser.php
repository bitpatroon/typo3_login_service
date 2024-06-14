<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 12-09-2023 14:50
 *
 *  All rights reserved
 *
 *  This script is part of a client or private project.
 *  You can redistribute it and/or modify it under the terms of
 *  the GNU General Public License as published by the Free
 *  Software Foundation; either version 3 of the License, or
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

namespace BPN\Typo3LoginService\Traits;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait HasLoggedInUser
{
    /**
     * @return bool|int
     */
    public function getCurrentlyLoggedInUserId()
    {
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        return $context->getPropertyFromAspect('frontend.user', 'id');
    }

    /**
     * Tests if the current user is logged in.
     *
     * @param bool $checkStrict
     *
     * @return bool
     */
    public function isCurrentFrontendUserLoggedIn(bool $checkStrict = false)
    {
        if ($this->getCurrentlyLoggedInUserId()) {
            if ($checkStrict) {
                return (bool)($_COOKIE['fe_typo_user'] ?? false);
            }

            return true;
        }

        return false;
    }

}
