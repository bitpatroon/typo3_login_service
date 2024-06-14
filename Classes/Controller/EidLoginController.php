<?php

namespace BPN\Typo3LoginService\Controller;

use BPN\Typo3LoginService\Traits\RequiresBootstrap;
use BPN\Typo3LoginService\Traits\RequiresRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequestFactory;

class EidLoginController
{
    use RequiresBootstrap;
    use RequiresRequestHandler;

    /**
     * Initialises the FE user, if the user is not already initialized
     *
     * @return bool|null
     * @see /eID/tx_typo3loginservice/a/loginuser/
     */
    public function initFeUser()
    {
        $globalRequest = ServerRequestFactory::fromGlobals();

        $eidLoginRequestHandler = $this->getEidLoginRequestHandler();

        if (!$eidLoginRequestHandler->canHandleRequest($globalRequest)) {
            return null;
        }

        $eidLoginRequestHandler->handleRequest($globalRequest);

        return $eidLoginRequestHandler->isCurrentFrontendUserLoggedIn();
    }
}