<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 2-9-2019 16:31
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

namespace BPN\Typo3LoginService\RequestHandler\v8;

use Bitpatroon\Typo3Hooks\Helpers\HooksHelper;
use BPN\Typo3LoginService\LoginService\CodeLoginService;
use BPN\Typo3LoginService\Traits\HasVersion;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Http\RequestHandler;

/**
 * @deprecated Depends on v8 of Typo3
 */
class CodeLoginRequestHandler extends RequestHandler
{
    use HasVersion;

    /** @var bool */
    protected $disableFetchUser;
    /** @var bool */
    protected $shouldLogOff;

    public function shouldLogOff()
    {
        $this->shouldLogOff = true;

        return $this;
    }

    /**
     * Handles a frontend request
     *
     * @param ServerRequestInterface $request
     *
     * @return NULL|ResponseInterface
     */
    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $controller = $this->getController();

        // temporarily add the (new) login service!
        $this->registerService();

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'])) {
            // enable auto-login
            $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] = 1;
        }

        $this->disableFetchUser()
             ->logOff();

        $_GET = $_GET ?? [];
        $_GET['logintype'] = 'login';
        $controller->fe_user->newSessionID = true;
        $controller->initUserGroups();



        return new NullResponse();
    }

    private function logOff()
    {
        if ($this->shouldLogOff) {
            $controller = $this->getController();
            if ($controller->fe_user) {
                // disable hook(s)
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_pre_processing'] = null;
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'] = null;

                HooksHelper::processHook($this, 'on_before_logging_off');

//            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\SPL\SplExtUpdates\T3Hooks\FrontendUserAuthentication::class]['on_before_logoff'] = null;
//            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\SPL\SplExtUpdates\T3Hooks\FrontendUserAuthentication::class]['on_after_logoff'] = null;

                $controller->fe_user->logoff();
            }
        }

        return $this;
    }

    /**
     * This request handler can handle any frontend request.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool If the request is not an eID request, TRUE otherwise FALSE
     */
    public function canHandleRequest(ServerRequestInterface $request): bool
    {
        return true;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getController()
    {
        if (!$GLOBALS['TSFE']) {
            parent::initializeController();
        }

        if (!$this->controller) {
            $this->controller = $GLOBALS['TSFE'];
        }

        return $this->controller;
    }

    /**
     * registers the service for logging in by code
     */
    protected function registerService()
    {
        // register the service
        ExtensionManagementUtility::addService(
            'typo3_login_service',
            'auth',
            CodeLoginService::class,
            [
                'title' => 'CodeLoginServices',
                'description' => '',
                'subtype' => 'getUserFE,authUserFE',
                'available' => true,
                'priority' => 201,
                'quality' => 201,
                'os' => '',
                'exec' => '',
                'className' => CodeLoginService::class,
            ]
        );
    }

    public function shouldDisableFetchUser()
    {
        $this->disableFetchUser = true;

        return $this;
    }

    public function disableFetchUser()
    {
        if ($this->disableFetchUser) {
            $controller = $this->getController();
            if (!($controller->fe_user->svConfig ?? null)) {
                $controller->fe_user->svConfig[] = [
                    'setup' => [
                        'FE_fetchUserIfNoSession' => 1,
                    ],
                ];
            }
        }

        return $this;
    }

    private function enableAutoLogin()
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'])) {
            // enable auto-login
            $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] = 1;
        }

        return $this;
    }


}