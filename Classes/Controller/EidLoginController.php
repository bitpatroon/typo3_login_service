<?php

namespace BPN\Typo3LoginService\Controller;

use SPL\SplLibrary\AccessControl\AuthorizationService;
use BPN\Typo3LoginService\RequestHandler\EidLoginRequestHandler;
use SPL\SplLibrary\Utility\ObjectManagerHelper;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EidLoginController extends AbstractLoginController
{
    /**
     * Initialises the FE user, if the user is not already initiliased
     * @return bool|null
     * @see /eID/tx_typo3loginservice/a/loginuser/
     */
    public function initFeUser()
    {
        $globalRequest = ServerRequestFactory::fromGlobals();

        $bootstrap = Bootstrap::getInstance();

        /** @var EidLoginRequestHandler $eidLoginRequestHandler */
        $eidLoginRequestHandler = GeneralUtility::makeInstance(EidLoginRequestHandler::class, $bootstrap);

        if (!$eidLoginRequestHandler->canHandleRequest($globalRequest)) {
            return null;
        }

        $eidLoginRequestHandler->handleRequest($globalRequest);

        /** @var AuthorizationService $authorizationService */
        $authorizationService = ObjectManagerHelper::get(AuthorizationService::class);
        return $authorizationService->isCurrentFrontendUserLoggedIn();
    }
}