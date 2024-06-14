<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 12-09-2023 14:56
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

trait HasVersion
{
    protected function getBranch()
    {
        [$major, $minor,] = explode('.', $this->getTypo3Version());
        if ($major && $minor) {
            return $major . '.' . $minor;
        }

        return $major ?? '';
    }

    /**
     * @return int
     */
    protected function getMajor()
    {
        [$major,] = explode('.', $this->getTypo3Version());

        return (int)($major ?? 0);
    }

    protected function getTypo3Version()
    {
        if (defined('TYPO3_version')) {
            return TYPO3_version;
        }
        if (defined('VERSION')) {
            return TYPO3_version;
        }

        throw new \RuntimeException(
            'Cannot determine version',
            1694515972819
        );
    }
}
