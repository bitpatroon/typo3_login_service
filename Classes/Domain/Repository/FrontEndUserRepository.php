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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontEndUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
{
    /**
     * Gets the userId
     *
     * @param string $userId the usernaem or uid
     *
     * @return int
     */
    public function getUserId(string $userId)
    {
        $user = $this->findUser($userId);
        if ($user) {
            return (int)$user['uid'];
        }

        return 0;
    }

    /**
     * @param string $username
     *
     * @return bool|int false if not found. The uid otherwise
     * @deprecated \BPN\Typo3LoginService\Domain\Repository\FrontEndUserRepository::getUserId
     */
    public function getByUserName(string $username)
    {
        return $this->getUserId($username);
    }

    /**
     * @param int $uid
     *
     * @return bool|array the user record or false if not found
     */
    public function getByUid(int $uid = 0)
    {
        return $this->findUser((string)$uid);
    }

    /**
     *  Gets the record by uid or name
     *
     * @param string $userId       A user id or a username
     * @param bool   $allowExpired true to allow expired to be added to the collection of users
     *
     * @return array|null
     */
    public function findUser(string $userId, bool $allowExpired = false)
    {
        if (!$userId) {
            return null;
        }

        $table = 'fe_users';

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                                      ->getQueryBuilderForTable($table);

        if ($allowExpired) {
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        }

        $where = [];
        if (is_numeric($userId)) {
            $where[] = $queryBuilder->expr()->eq('uid', (int)$userId);
        } else {
            $where[] = $queryBuilder->expr()->orx(
                $queryBuilder->expr()->eq(
                    'username',
                    $queryBuilder->createNamedParameter($userId, Connection::PARAM_STR)
                ),
            );
        }

        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(...$where);

        return $queryBuilder->execute()->fetch() ?? null;
    }


}