<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 3-9-2019 17:42
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
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use BPN\Typo3LoginService\Controller\CodeLoginController;
use PHPUnit\Framework\Assert;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class CodeLoginControllerTest extends FunctionalTestCase
{
    protected function setUp()
    {
        define('TYPO3_ERROR_DLOG', 0);

        parent::setUp();

        $this->importDataSet(dirname(__DIR__) . '/Fixtures/LoginControllerTestFixture.xml');
    }

    /**
     * @test
     * @param int  $userId
     * @param bool $expectedResult
     * @dataProvider dataProvider_eIDInitWorksAsExpected
     */
    public function loginUserWorksAsExpected($userId, $expectedResult)
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/index.php';

        /** @var CodeLoginController $loginController */
        $loginController = GeneralUtility::makeInstance(CodeLoginController::class);
        Assert::assertEquals($expectedResult, $loginController->loginUser($userId), sprintf("User with id %d is not with success logged in", $userId));
    }

    /**
     * @return array
     */
    public function dataProvider_eIDInitWorksAsExpected()
    {
        return [
            [1, true],
            ['authenticated_user', true],
            [2, false],
            ['unknown_user', false]
        ];
    }


}