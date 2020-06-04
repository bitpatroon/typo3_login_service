<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 8-4-2020 21:39
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

namespace BPN\Typo3LoginService\RequestHandler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LoginRequestHandler
 * @package SPL\SplLibrary\RequestHandler
 */
class EidLoginRequestHandler extends CodeLoginRequestHandler
{

    /**
     * This request handler can handle any frontend request.
     *
     * @param ServerRequestInterface $request
     * @return bool If the request is not an eID request, TRUE otherwise FALSE
     */
    public function canHandleRequest(ServerRequestInterface $request)
    {
        $isEid = !empty($request->getQueryParams()['eID']) || !empty($request->getParsedBody()['eID']);
        if (!$isEid) {
            return false;
        }

        // Ensure the user is logged in. If so no further action
        if (!empty($GLOBALS['TSFE'])) {
            return empty($GLOBALS['TSFE']->loginUser);
        }

        return true;
    }
}
