<?php

namespace BPN\Typo3LoginService\Controller\Login;

use BPN\Typo3LoginService\RequestHandler\EidLoginRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EidLoginController extends AbstractLoginController
{
    /**
     * Initialises the FE user, if the user is not already initiliased.
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