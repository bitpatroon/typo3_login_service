<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 8-4-2020 21:38
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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository as FrontendUserRepositoryAlias;

class FrontEndUserRepository extends FrontendUserRepositoryAlias
{
    const TABLE = 'fe_users';

    /**
     * @param string $username
     * @return bool|int false if not found. The uid otherwise
     */
    public function getByUserName($username)
    {
        if (empty($username)) {
            return false;
        }

        $table = self::TABLE;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username))
            );

        // retrieve single record
        $row = $queryBuilder->execute()->fetch();
        if (empty($row)) {
            return 0;
        }

        return (int)$row['uid'];
    }

    /**
     * @param int $uid
     * @return bool|array
     */
    public function getByUid($uid)
    {
        if (empty($uid)) {
            return false;
        }

        $table = self::TABLE;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid', $uid)
            );

        // retrieve single record
        $row = $queryBuilder->execute()->fetch();
        return empty($row) ? 0 : $row;
    }
}
