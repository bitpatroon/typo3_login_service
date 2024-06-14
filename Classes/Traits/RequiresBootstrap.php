<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 05-01-2024 16:23
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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\Bootstrap;

trait RequiresBootstrap
{
    private function app()
    {
        // ---------------------------------
        // TODO: FIX-FOR-UPGRADE-9
        // --------------------------------

        $classLoader = require $this->projectRoot('/vendor/autoload.php');

        return Bootstrap::init($classLoader);
    }

    private function projectRoot(string $append = '')
    {
        return Environment::getProjectPath() . $append;
    }
}
