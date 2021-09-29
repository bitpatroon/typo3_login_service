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

use Bitpatroon\Typo3Hooks\Helpers\HooksHelper;
use BPN\Typo3LoginService\LoginService\CodeLoginService;
use BPN\Typo3LoginService\LoginService\CodeUserAuthenticationAuthentication;
use BPN\Typo3LoginService\Services\UserService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class CodeLoginRequestHandler
{
    /**
     * Handles a frontend request.
     */
    public function handleRequest()
    {
        $controller = $this->getTypoScriptFrontendController();

        // temporarily add the (new) login service!
        $this->registerService();

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'])) {
            // enable auto-login
            $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] = 1;
        }

        /** @var UserService $usersService */
        $usersServices = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(UserService::class);

        if ($usersServices->isLoggedIn()) {
            // disable hook(s)
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_pre_processing'] = null;
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'] = null;
            HooksHelper::processHook($this, 'on_before_logging_off');
            $controller->fe_user->logoff();
        }

        /** @var CodeUserAuthenticationAuthentication $frontendUserAuthentication */
        $frontendUserAuthentication = GeneralUtility::makeInstance(CodeUserAuthenticationAuthentication::class);
        $frontendUserAuthentication->start();

        return $frontendUserAuthentication->authenticated;
    }

    /**
     * registers the service for logging in by code.
     */
    protected function registerService()
    {
        // register the service
        ExtensionManagementUtility::addService(
            'typo3_login_service',
            'auth',
            CodeLoginService::class,
            [
                'title'       => 'CodeLoginServices',
                'description' => '',
                'subtype'     => 'getUserFE,authUserFE',
                'available'   => true,
                'priority'    => 201,
                'quality'     => 201,
                'os'          => '',
                'exec'        => '',
                'className'   => CodeLoginService::class,
            ]
        );
    }

    public function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        if (!empty($GLOBALS['TSFE']) && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            return $GLOBALS['TSFE'];
        }

        // This usually happens when typolink is created by the TYPO3 Backend, where no TSFE object
        // is there. This functionality is currently completely internal, as these links cannot be
        // created properly from the Backend.
        // However, this is added to avoid any exceptions when trying to create a link.
        // Detecting the "first" site usually comes from the fact that TSFE needs to be instantiated
        // during tests
        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            $sites = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites();
            $site = reset($sites);
            if (!$site instanceof Site) {
                $site = new NullSite();
            }
        }
        $language = $request->getAttribute('language');
        if (!$language instanceof SiteLanguage) {
            $language = $site->getDefaultLanguage();
        }

        $id = $request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? $site->getRootPageId();
        $type = $request->getQueryParams()['type'] ?? $request->getParsedBody()['type'] ?? '0';

        $typoScriptFrontendController = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            GeneralUtility::makeInstance(Context::class),
            $site,
            $language,
            $request->getAttribute('routing', new PageArguments((int) $id, (string) $type, []))
        );
        $typoScriptFrontendController->sys_page = GeneralUtility::makeInstance(PageRepository::class);
        $typoScriptFrontendController->tmpl = GeneralUtility::makeInstance(TemplateService::class);

        $GLOBALS['TSFE'] = $typoScriptFrontendController;

        return $typoScriptFrontendController;
    }

    /**
     * Invokes protected / private method.
     *
     * @param object $instance   reference to object
     * @param string $methodName method name
     * @param array  $arguments  arguments
     *
     * @return mixed result of the original call
     */
    public function invokeProtected(&$instance, $methodName, $arguments = [])
    {
        /** @var \ReflectionMethod $methodReflection */
        $methodReflection = new \ReflectionMethod($instance, $methodName);
        $methodReflection->setAccessible(true);

        return $methodReflection->invokeArgs($instance, $arguments);
    }
}
