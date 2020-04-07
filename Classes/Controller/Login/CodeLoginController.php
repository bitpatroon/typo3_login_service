<?php


namespace BPN\Typo3LoginService\Controller\Login;


use BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository;
use BPN\Typo3LoginService\LoginService\CodeLoginService;
use BPN\Typo3LoginService\RequestHandler\CodeLoginRequestHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CodeLoginController extends AbstractLoginController
{
    /**
     * Method logs a user in
     * @param int|string $uidOrUsername
     * @return array|bool|null
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function loginUser($uidOrUsername)
    {
        if (empty($uidOrUsername)) {
            return ['error' => 'Missing value for userid', 'ts' => '1567503579121'];
        }

        $uid = $this->getUid($uidOrUsername);

        /** @var ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

        /** @var CodeLoginService $codeLoginService */
        $codeLoginService = $objectManager->get(CodeLoginService::class);
        $codeLoginService->setTargetUserId($uid);

        /** @var CodeLoginRequestHandler $requestHandler */
        $requestHandler = GeneralUtility::makeInstance(CodeLoginRequestHandler::class);
        $requestHandler->handleRequest();

        return $this->isCurrentFrontendUserLoggedIn();
    }

    /**
     * Gets the uid
     * @param string|int $uidOrUsername
     * @return bool|int
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    private function getUid($uidOrUsername)
    {
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