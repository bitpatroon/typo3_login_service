<?php


namespace BPN\Typo3LoginService\Controller;


use BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository;
use BPN\Typo3LoginService\LoginService\CodeLoginService;
use SPL\SplLibrary\AccessControl\AuthorizationService;
use BPN\Typo3LoginService\RequestHandler\CodeLoginRequestHandler;
use SPL\SplLibrary\Utility\ObjectManagerHelper;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CodeLoginController extends AbstractLoginController
{
    /**
     * Method logs a user in
     * @param int|string  $uidOrUsername
     * @return array|bool|null
     */
    public function loginUser($uidOrUsername)
    {
        if(empty($uidOrUsername)){
            return ['error' => 'Missing value for userid', 'ts' => '1567503579121'];
        }

        $uid = $this->getUid($uidOrUsername);

        /** @var ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

        /** @var CodeLoginService $codeLoginService */
        $codeLoginService = $objectManager->get(CodeLoginService::class);
        $codeLoginService->setTargetUserId($uid);

        $globalRequest = ServerRequestFactory::fromGlobals();
        $bootstrap = Bootstrap::getInstance();

        /** @var CodeLoginRequestHandler $requestHandler */
        $requestHandler = GeneralUtility::makeInstance(CodeLoginRequestHandler::class, $bootstrap);

        if (!$requestHandler->canHandleRequest($globalRequest)) {
            return null;
        }

        $requestHandler->handleRequest($globalRequest);

        /** @var AuthorizationService $authorizationService */
        $authorizationService = ObjectManagerHelper::get(AuthorizationService::class);
        return $authorizationService->isCurrentFrontendUserLoggedIn();
    }

    /**
     * Gets the uid
     * @param string|int $uidOrUsername
     * @return bool|int
     */
    private function getUid($uidOrUsername){
        $uid = $uidOrUsername;
        if (!is_numeric($uidOrUsername)) {
            /** @var ObjectManager $objectManager */
            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

            /** @var FrontEndUserRepository $frontEndUserRepository */
            $frontEndUserRepository = $objectManager->get(FrontEndUserRepository::class);
            $uid = $frontEndUserRepository->getByUserName($uidOrUsername);
        }

        return $uid;
    }
}