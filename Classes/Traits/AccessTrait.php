<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 13-06-2023 12:07
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

trait AccessTrait
{
    public function isTestingEnvironment()
    {
        return $this->isDevEnvironment() || $this->isTestEnvironment();
    }

    /**
     * Tests if the environment is Test
     *
     * @return bool
     */
    public function isDevEnvironment()
    {
        $otap = $this->getOtap();

        return $otap === 'O';
    }

    /**
     * Tests if the environment is Test environment
     *
     * @return bool
     */
    public function isTestEnvironment()
    {
        $otap = $this->getOtap();

        return $otap === 'T';
    }

    /**
     * Gets the otap setting
     *
     * @return array|false|string
     */
    public function getOtap()
    {
        if (defined('SPL_OTAP') && (SPL_OTAP ?? '')) {
            return SPL_OTAP;
        }

        return getenv('SPL_OTAP');
    }
}
