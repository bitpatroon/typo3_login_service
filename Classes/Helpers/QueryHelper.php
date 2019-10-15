<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 14-10-2019 20:53
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

namespace BPN\Typo3LoginService\Helpers;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class QueryHelper implements SingletonInterface
{
    /**
     * @param string $table
     * @param bool   $allowHidden
     * @param bool   $allowExpired
     * @param bool   $ignoreGroupChecks
     * @return string
     */
    public function getEnableFields($table, $allowHidden = false, $allowExpired = false, $ignoreGroupChecks = false)
    {
        if (TYPO3_MODE === 'BE') {
            $enableFields = BackendUtility::BEEnableFields($table, 0);
            return $enableFields . BackendUtility::deleteClause($table);
        }

        if (TYPO3_MODE === 'FE') {


            $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            return $cObj->enableFields($table, $allowHidden, self::getIgnoreArray($allowExpired, $ignoreGroupChecks, $allowHidden));
        }

        return '';
    }

    /**
     * @return QueryHelper|object
     */
    public static function getInstance()
    {
        /** @var QueryHelper $ */
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(QueryHelper::class);
    }

    /**
     * Gets the enable fields ignorearray
     * @param bool $allowExpired      true to include expired. (Ignoring the stop / end time)
     * @param bool $ignoreGroupChecks true to include all group checks
     * @param bool $allowHidden       true to include hidden.
     * @return array the ignore array
     */
    private static function getIgnoreArray($allowExpired, $ignoreGroupChecks, $allowHidden)
    {
        $result = [];

        if ($allowExpired) {
            $result['starttime'] = 1;
            $result['endtime'] = 1;
        }
        if ($ignoreGroupChecks) {
            $result['fe_group'] = 1;
        }
        if ($allowHidden) {
            $result['disabled'] = 1;
        }

        return $result;
    }
}