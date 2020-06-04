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

namespace BPN\Typo3LoginService\Tests\Functional\Controller\CodeLoginController;

use BPN\Typo3LoginService\Controller\Login\CodeLoginController;
use PHPUnit\Framework\Assert;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class CodeLoginControllerTest extends FunctionalTestCase
{

    /** @noinspection PhpUnused */
    protected function setUp(): void
    {
        define('TYPO3_ERROR_DLOG', 0);

        parent::setUp();

        $this->testExtensionsToLoad[] = 'web/typo3conf/ext/typo3_login_service';

        $this->importDataSet(dirname(__DIR__) . '/Fixtures/LoginControllerTestFixture.xml');
    }

    /**
     * @test
     * @param int  $userId
     * @param bool $expectedResult
     * @dataProvider dataProviderEIDInitWorksAsExpected
     */
    public function loginUserWorksAsExpected($userId, $expectedResult)
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/index.php';

        /** @var CodeLoginController $loginController */
        $loginController = GeneralUtility::makeInstance(CodeLoginController::class);
        Assert::assertEquals(
            $expectedResult,
            $loginController->loginUser($userId),
            sprintf('User with id %d is not with success logged in', $userId)
        );
    }

    /**
     * @return array
     */
    public function dataProviderEIDInitWorksAsExpected()
    {
        return [
            [1, true],
            ['authenticated_user', true],
            [2, false],
            ['unknown_user', false]
        ];
    }
}
