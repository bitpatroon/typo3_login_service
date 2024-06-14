<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 13-06-2023 10:35
 *
 *  All rights reserved
 *
 *  This script is part of a client or private project.
 *  You can redistribute it and/or modify it under the terms of
 *  the GNU General Public License as published by the Free
 *  Software Foundation; either version 3 of the License, or
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

namespace BPN\Typo3LoginService\LoginService;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use BPN\Typo3LoginService\Traits\AccessTrait;
use BPN\Typo3LoginService\Traits\InitiatedBy;
use SPL\Entree\Domain\Model\AttributesModel;
use SPL\SplLibrary\AccessControl\AuthorizationService;
use SPL\SplLibrary\Utility\SamlHelper;
use TYPO3\CMS\Core\Service\AbstractService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Logs in the user by attributes in a cookie federation
 */
class AttributeLoginService extends AbstractService implements SingletonInterface
{
    use AccessTrait;
    use InitiatedBy;

    const PRAKTIJKLEREN_ORGANISATIE = 'Stichting Praktijkleren';
    const ENTREE_REFERENTIE_TEST_ORGANISATIE = 'Entree Referentie Organisatie';
    const ENTREE_REFERENTIE_TEST_ORGANISATIE_ID = 'REF1';
    /**
     * @var array|mixed
     */
    protected $attributes = null;

    /**
     * Initialize authentication service. (only called when not logged in yet)
     *
     * @param string $mode      Subtype of the service which is used to call the service.
     * @param array  $loginData Submitted login form data.
     * @param array  $authInfo  Information array. Holds submitted form data etc.
     * @param object $pObj      Parent object.
     */
    public function initAuth($mode, $loginData, $authInfo, $pObj)
    {
        if ($this->isForService()) {
            $this->attributes[AttributesModel::FIELD_FEDERATED_BY] = [AttributesModel::FIELD_FEDERATED_BY_MANUAL];
            // if only value is '-', make empty!
            if ($this->attributes[AttributesModel::FIELD_ENTREEUID] === ['-']) {
                $this->attributes[AttributesModel::FIELD_UID] = [];
                $this->attributes[AttributesModel::FIELD_ENTREEUID] = [];
            }
        }
    }

    /**
     * Find a user. (eg. look up the user record in database when a login is sent)
     *
     * @return    mixed User array or FALSE.
     * @throws \Exception
     */
    public function getUser()
    {
        if ($this->isForService()) {
            $userName = current($this->attributes[AttributesModel::FIELD_NL_EDU_PERSON_PROFILE_ID]);

            $user = $this->findUser($userName);

            self::$initiatedByMe = true;

            return $user ?? false;
        }

        return [];
    }

    /**
     * Authenticates user.
     *
     * @param array $userRecord User record
     *
     * @return int Code that shows if user is really authenticated.
     */
    public function authUser(array $userRecord): int
    {
        if (self::isInitiatedByMe()) {
            // authenticate!
            return 200;
        }

        // 100 means "we do not know, continue"
        return 100;
    }

    /**
     * @return bool true if the attributes are for this service or not!
     */
    public function isForService()
    {
        if (!$this->isTestingEnvironment()) {
            return false;
        }

        $this->attributes = $this->attributes ?? (SamlHelper::getSamlAttributesCookie() ?? []);
        if (!$this->attributes) {
            return false;
        }

        if (!$this->originatesFromSelf()) {
            return false;
        }

        return in_array(
            AttributesModel::FIELD_FEDERATE_FOR_ATTRIBUTES,
            $this->attributes[AttributesModel::FIELD_FEDERATE_FOR]
        );
    }

    public function originatesFromSelf()
    {
        if ($encryptionKey = ($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] ?? false)) {
            $hash = current($this->attributes['hash']);
            /** @var AuthorizationService $authorizationService */
            $authorizationService = GeneralUtility::makeInstance(AuthorizationService::class);
            if ($clearText = $authorizationService->decrypt(
                $hash,
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
            )) {
                return $clearText === 'valid';
            }
        }

        return false;
    }

    public function findUser(string $username = '')
    {
        $table = 'fe_users';

        /** @var Connection $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(
            ConnectionPool::class
        )
                                                              ->getConnectionForTable($table);

        $data = $queryBuilder
            ->select(['*'], $table, ['username' => $username])
            ->fetch();

        if ($data) {
            return $data;
        }

        return $queryBuilder
            ->select(['*'], $table, ['email' => $username])
            ->fetch();
    }

}
