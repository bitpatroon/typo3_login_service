<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 2-9-2019 16:21
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

namespace BPN\Typo3LoginService\Domain\Repository;

class FrontEndUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
{

    /**
     * @param string $username
     * @return bool|int false if not found. The uid otherwise
     */
    public function getByUserName($username){
        if(empty($username)){
            return false;
        }

        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $dbHandle */
        $dbHandle = $GLOBALS['TYPO3_DB'];
        $row = $dbHandle->exec_SELECTgetSingleRow(
            'uid',
            'fe_users',
            implode(' AND ', [
                sprintf('%s=%s', 'username', $dbHandle->fullQuoteStr($username ?: '', 'fe_users')),
                \BPN\Typo3LoginService\Helpers\QueryHelper::getInstance()->getEnableFields('fe_users')
            ])
        );

        if(empty($row)){
            return 0;
        }

        return (int)$row['uid'];
    }

    /**
     * @param int $uid
     * @return bool|array the user record or false if not found
     */
    public function getByUid($uid){
        if(empty($uid)){
            return false;
        }

        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $dbHandle */
        $dbHandle = $GLOBALS['TYPO3_DB'];
        return $dbHandle->exec_SELECTgetSingleRow(
            '*',
            'fe_users',
            implode(' AND ', [
                sprintf('%s=%s', 'uid', (int)$uid),
                \BPN\Typo3LoginService\Helpers\QueryHelper::getInstance()->getEnableFields('fe_users')
            ])
        );
    }

}