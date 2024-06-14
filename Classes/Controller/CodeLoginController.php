<?php

namespace BPN\Typo3LoginService\Controller;

use BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository;
use BPN\Typo3LoginService\LoginService\CodeLoginService;
use BPN\Typo3LoginService\Traits\HasLoggedInUser;
use BPN\Typo3LoginService\Traits\HasVersion;
use BPN\Typo3LoginService\Traits\RequiresBootstrap;
use BPN\Typo3LoginService\Traits\RequiresRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CodeLoginController
{
    use HasVersion;
    use HasLoggedInUser;
    use RequiresBootstrap;
    use RequiresRequestHandler;

    /**
     * Method logs a user in
     *
     * @param string $userId
     *
     * @return array|bool|null
     */
    public function loginUser(string $userId)
    {
        if (!$userId) {
            return ['error' => 'Missing value for userid', 'ts' => '1567503579121'];
        }

        $uid = $this->getUid($userId);

        /** @var CodeLoginService $codeLoginService */
        $codeLoginService = GeneralUtility::makeInstance(ObjectManager::class)
                                          ->get(CodeLoginService::class);
        $codeLoginService->setTargetUserId($uid);

        $requestHandler = $this->getCodeRequestHandler();
        $globalRequest = ServerRequestFactory::fromGlobals();
        if (!$requestHandler->canHandleRequest($globalRequest)) {
            return null;
        }

        $requestHandler
            ->shouldLogOff()
            ->handleRequest($globalRequest);

        return $this->isCurrentFrontendUserLoggedIn();
    }

    /**
     * Gets the uid
     *
     * @param string $userId
     *
     * @return int
     */
    private function getUid(string $userId)
    {
        if (is_numeric($userId)) {
            return (int)$userId;
        }

        /** @var FrontEndUserRepository $frontEndUserRepository */
        $frontEndUserRepository = GeneralUtility::makeInstance(ObjectManager::class)
                                                ->get(FrontEndUserRepository::class);

        return $frontEndUserRepository->getUserId($userId) ?? 0;
    }



}